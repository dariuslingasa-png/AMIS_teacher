<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject as SubjectModel;
use Illuminate\Support\Collection;

class AdminAcademicController extends Controller
{
    public function dashboard()
    {
        $schoolYear = (string) config('services.school.year', '2026-2027');
        $subjects = SubjectModel::orderBy('grade_level')->get();
        $sections = Section::withCount('students')->get();

        $elementaryGrades = ['Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $highSchoolGrades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $allGrades = array_merge($elementaryGrades, $highSchoolGrades);

        $academicStats = [
            'subjects' => $subjects->count(),
            'sections' => $sections->count(),
            'students' => $sections->sum('students_count'),
            'school_year' => $schoolYear,
        ];

        $academicCharts = [
            'subjectDivision' => [
                'labels' => ['Elementary', 'High School'],
                'data' => [
                    $subjects->whereIn('grade_level', $elementaryGrades)->count(),
                    $subjects->whereIn('grade_level', $highSchoolGrades)->count(),
                ],
            ],
            'sectionMode' => [
                'labels' => ['Face to Face', 'Flexible - 1st Shift', 'Flexible - 2nd Shift'],
                'data' => [
                    $sections->filter(fn ($section) => str_contains(strtolower((string) $section->learning_mode), 'face') || strtoupper((string) $section->shift) === 'F2F')->count(),
                    $sections->filter(fn ($section) => str_contains(strtolower((string) $section->learning_mode), 'flexible') && str_contains(strtolower((string) $section->shift), '1'))->count(),
                    $sections->filter(fn ($section) => str_contains(strtolower((string) $section->learning_mode), 'flexible') && str_contains(strtolower((string) $section->shift), '2'))->count(),
                ],
            ],
            'gradeSubjects' => [
                'labels' => $allGrades,
                'data' => collect($allGrades)->map(fn ($grade) => $subjects->where('grade_level', $grade)->count())->values(),
            ],
            'gradeSections' => [
                'labels' => $allGrades,
                'data' => collect($allGrades)->map(fn ($grade) => $sections->where('grade_level', $grade)->count())->values(),
            ],
        ];

        return view('admin.academic.dashboard', compact('academicStats', 'academicCharts'));
    }

    public function subjects()
    {
        $subjects = SubjectModel::orderBy('grade_level')->get();

        return view('admin.academic.subjects', compact('subjects'));
    }

    public function curriculum()
    {
        $schoolYear = (string) config('services.school.year', '2026-2027');
        $subjects = SubjectModel::orderBy('grade_level')->get();
        $sections = Section::withCount('students')->get();
        $schoolYears = $this->schoolYearRows($sections, $schoolYear);
        $events = [];

        return view('admin.academic.curriculum', compact('subjects', 'sections', 'schoolYears', 'events'));
    }

    public function teachers()
    {
        $teachers = [];

        return view('admin.academic.teachers', compact('teachers'));
    }

    public function schedules()
    {
        $sections = Section::all();
        $teachers = [];

        return view('admin.academic.schedules', compact('sections', 'teachers'));
    }

    public function schoolYears()
    {
        $schoolYear = (string) config('services.school.year', '2026-2027');
        $schoolYears = $this->schoolYearRows(Section::withCount('students')->get(), $schoolYear);

        return view('admin.academic.school-years', compact('schoolYears'));
    }

    public function calendar()
    {
        $events = [];

        return view('admin.academic.calendar', compact('events'));
    }

    public function operations()
    {
        $sections = Section::withCount('students')->get();

        $attendance = [
            'rate' => 0,
            'present' => 0,
            'absent' => 0,
            'excused' => 0,
            'by_grade' => $sections->groupBy('grade_level')->map(fn () => 0)->all(),
        ];

        $grades = [
            'submitted' => 0,
            'pending' => $sections->count(),
            'total' => $sections->count(),
            'sections' => $sections->map(fn ($section) => [
                'name' => $section->name,
                'status' => 'Pending',
                'date' => '-',
            ])->values()->all(),
        ];

        $reports = [];

        return view('admin.academic.operations', compact('attendance', 'grades', 'reports'));
    }

    private function schoolYearRows(Collection $sections, string $schoolYear): array
    {
        return [
            [
                'year' => $schoolYear,
                'semester' => '1st Semester',
                'status' => 'Active',
                'enrolled' => $sections->sum('students_count'),
            ],
        ];
    }
}
