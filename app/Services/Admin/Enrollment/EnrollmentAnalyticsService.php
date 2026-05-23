<?php

namespace App\Services\Admin\Enrollment;

use App\Models\EnrollmentApplicant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class EnrollmentAnalyticsService
{
    public function reportQuery(Request $request): Builder
    {
        $query = EnrollmentApplicant::with('user', 'payment')
            ->whereNotIn('status', ['draft']);

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('grade')) {
            $query->where('grade_level', (string) $request->input('grade'));
        }

        if ($request->filled('type')) {
            $query->where('student_type', (string) $request->input('type'));
        }

        if ($request->filled('payment_status')) {
            $paymentStatus = (string) $request->payment_status;

            if ($paymentStatus === 'missing') {
                $query->where(function (Builder $query) {
                    $query->whereDoesntHave('payment')
                        ->orWhereHas('payment', fn (Builder $payment) => $payment->whereNull('receipt_url'));
                });
            } else {
                $query->whereHas('payment', fn (Builder $payment) => $payment->where('status', $paymentStatus));
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', (string) $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', (string) $request->input('to'));
        }

        return $query;
    }

    public function locationCounts(string $column): Collection
    {
        return EnrollmentApplicant::select($column, DB::raw('COUNT(*) as total'))
            ->whereNotIn('status', ['draft'])
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(12)
            ->pluck('total', $column);
    }

    public function gradeSlotData(string $schoolYear, Collection $gradeCounts): Collection
    {
        if (!Schema::hasTable('grade_levels')) {
            return $gradeCounts->map(fn ($count, $grade) => [
                'grade' => $grade ?: 'Not Set',
                'capacity' => 0,
                'enrolled' => (int) $count,
                'available' => 0,
                'used_percent' => 0,
                'status' => 'No slot config',
            ])->values();
        }

        return DB::table('grade_levels')
            ->where('school_year', $schoolYear)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($grade) use ($gradeCounts) {
                $capacity = (int) $grade->capacity;
                $enrolled = (int) $grade->enrolled_count;
                $available = max(0, $capacity - $enrolled);
                $usedPercent = $capacity > 0 ? min(100, round(($enrolled / $capacity) * 100)) : 0;
                $applicantCount = (int) ($gradeCounts[$grade->name] ?? 0);

                return [
                    'grade' => $grade->name,
                    'capacity' => $capacity,
                    'enrolled' => $enrolled,
                    'available' => $available,
                    'used_percent' => $usedPercent,
                    'applicant_count' => $applicantCount,
                    'status' => $available <= 0 ? 'Full' : ($available <= 5 ? 'Limited' : 'Open'),
                ];
            });
    }

    public function shiftSlotData(string $schoolYear): Collection
    {
        if (!Schema::hasTable('grade_shift_slots') || !Schema::hasTable('grade_levels') || !Schema::hasTable('enrollment_shifts')) {
            return collect();
        }

        return DB::table('grade_shift_slots as slots')
            ->join('grade_levels as grades', 'grades.id', '=', 'slots.grade_level_id')
            ->join('enrollment_shifts as shifts', 'shifts.id', '=', 'slots.enrollment_shift_id')
            ->where('slots.school_year', $schoolYear)
            ->where('slots.is_active', true)
            ->where('grades.is_active', true)
            ->where('shifts.is_active', true)
            ->orderBy('grades.sort_order')
            ->orderBy('shifts.start_time')
            ->get([
                'grades.name as grade',
                'shifts.name as shift',
                'shifts.start_time',
                'shifts.end_time',
                'slots.capacity',
                'slots.enrolled_count',
            ])
            ->map(function ($slot) {
                $capacity = (int) $slot->capacity;
                $enrolled = (int) $slot->enrolled_count;
                $available = max(0, $capacity - $enrolled);

                return [
                    'grade' => $slot->grade,
                    'shift' => $slot->shift,
                    'time' => substr((string) $slot->start_time, 0, 5).' - '.substr((string) $slot->end_time, 0, 5),
                    'capacity' => $capacity,
                    'enrolled' => $enrolled,
                    'available' => $available,
                    'used_percent' => $capacity > 0 ? min(100, round(($enrolled / $capacity) * 100)) : 0,
                    'status' => $available <= 0 ? 'Full' : ($available <= 5 ? 'Limited' : 'Open'),
                ];
            });
    }

    public function slotMatrixData(Collection $gradeSlots, Collection $shiftSlots): Collection
    {
        $shiftGroups = $shiftSlots->groupBy('grade');

        return $gradeSlots->map(function (array $gradeSlot) use ($shiftGroups) {
            $gradeShifts = $shiftGroups->get($gradeSlot['grade'], collect());

            return [
                'grade' => $gradeSlot['grade'],
                'applicant_count' => $gradeSlot['applicant_count'] ?? 0,
                'face_to_face' => $gradeSlot,
                'first_shift' => $this->findShiftSlot($gradeShifts, 'first'),
                'second_shift' => $this->findShiftSlot($gradeShifts, 'second'),
            ];
        });
    }

    private function findShiftSlot(Collection $slots, string $target): array
    {
        $slot = $slots->first(function (array $slot) use ($target) {
            $name = strtolower((string) $slot['shift']);

            return $target === 'first'
                ? str_contains($name, '1') || str_contains($name, 'first')
                : str_contains($name, '2') || str_contains($name, 'second');
        });

        if ($slot) {
            return $slot;
        }

        return [
            'capacity' => 0,
            'enrolled' => 0,
            'available' => 0,
            'used_percent' => 0,
            'status' => 'No slot config',
        ];
    }
}
