<?php

namespace App\Services;

use App\DTOs\AnnouncementData;
use App\DTOs\AssessmentData;
use App\DTOs\MeetingData;
use App\Models\LearningMaterial;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\SubjectAnnouncement;
use App\Models\SubjectMeeting;
use App\Models\TeacherSubjectAssignment;
use App\Models\Section;
use App\Services\MicrosoftGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TeacherPortalService
{
    protected MicrosoftGraphService $graph;

    public function __construct(MicrosoftGraphService $graph)
    {
        $this->graph = $graph;
    }
    public function getPortalData(Request $request): array
    {
        $subjects = $this->subjectsFor($request);
        $subjectIds = $subjects->pluck('subject_id')->filter()->unique();
        $sectionSubjectIds = $subjects->pluck('section_subject_id')->filter()->unique();

        $meetings = SubjectMeeting::query()
            ->where(fn ($query) => $query
                ->whereIn('section_subject_id', $sectionSubjectIds)
                ->orWhereIn('subject_id', $subjectIds))
            ->latest('meeting_date')
            ->get()
            ->map(fn (SubjectMeeting $meeting) => $this->meetingArray($meeting));

        $materials = LearningMaterial::query()
            ->where('visibility', 'published')
            ->where(fn ($query) => $query
                ->whereIn('section_subject_id', $sectionSubjectIds)
                ->orWhereIn('subject_id', $subjectIds))
            ->latest()
            ->get()
            ->map(fn (LearningMaterial $material) => $this->materialArray($material));

        $announcements = SubjectAnnouncement::query()
            ->where(fn ($query) => $query
                ->whereIn('section_subject_id', $sectionSubjectIds)
                ->orWhereIn('subject_id', $subjectIds))
            ->latest('published_at')
            ->get()
            ->map(fn (SubjectAnnouncement $announcement) => $this->announcementArray($announcement));

        $students = $this->studentsFor($subjects);

        return [
            'subjects' => $subjects->values()->all(),
            'meetings' => $meetings->values()->all(),
            'materials' => $materials->values()->all(),
            'assessments' => $request->session()->get('teacher_assessments', []),
            'students' => $students->values()->all(),
            'scores' => $request->session()->get('teacher_scores', []),
            'announcements' => $announcements->values()->all(),
        ];
    }

    public function workspace(Request $request, string $workspaceId): array
    {
        $data = $this->getPortalData($request);
        $subject = collect($data['subjects'])->firstWhere('id', $workspaceId);
        abort_unless($subject, 404, 'Subject workspace not found.');

        return $data + [
            'subject' => $subject,
            'subjectMeetings' => collect($data['meetings'])->where('subject_id', $workspaceId)->values(),
            'subjectMaterials' => collect($data['materials'])->where('subject_id', $workspaceId)->values(),
            'subjectAnnouncements' => collect($data['announcements'])->where('subject_id', $workspaceId)->values(),
            'subjectStudents' => collect($data['students'])->where('section_subject_id', $subject['section_subject_id'])->values(),
        ];
    }

    public function storeMeeting(Request $request, MeetingData $dto): void
    {
        $subject = $this->resolveSubject($request, $dto->subjectId);

        SubjectMeeting::create([
            'subject_id' => $subject['subject_id'],
            'section_subject_id' => $subject['section_subject_id'],
            'teacher_key' => $this->teacherKey($request),
            'teacher_name' => $request->session()->get('teacher_name', 'AMIS Teacher'),
            'teacher_email' => $request->session()->get('teacher_email'),
            'title' => $dto->title,
            'description' => $dto->description,
            'meeting_date' => $dto->date,
            'meeting_time' => $dto->time,
            'duration_minutes' => $dto->duration,
            'meeting_url' => $dto->link ?: ($dto->status->value === 'Live' ? 'https://teams.microsoft.com/' : null),
            'provider' => 'microsoft_teams',
            'status' => Str::lower($dto->status->value),
        ]);
    }

    public function storeMaterial(Request $request, array $data): void
    {
        $subject = $this->resolveSubject($request, $data['subject_id']);
        $file = $request->file('file');
        $path = $file ? $file->store('teacher-materials', 'public') : null;

        LearningMaterial::create([
            'subject_id' => $subject['subject_id'],
            'section_subject_id' => $subject['section_subject_id'],
            'teacher_key' => $this->teacherKey($request),
            'teacher_name' => $request->session()->get('teacher_name', 'AMIS Teacher'),
            'teacher_email' => $request->session()->get('teacher_email'),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $file ? 'file' : 'google_drive',
            'disk' => $file ? 'public' : null,
            'path' => $path,
            'external_url' => $data['external_url'] ?? null,
            'mime_type' => $file?->getClientMimeType(),
            'size_bytes' => $file?->getSize(),
            'visibility' => 'published',
        ]);
    }

    public function storeAssessment(Request $request, AssessmentData $dto): void
    {
        $items = $request->session()->get('teacher_assessments', []);
        array_unshift($items, array_merge(['id' => 'assessment-'.now()->timestamp], $dto->toArray()));
        $request->session()->put('teacher_assessments', $items);
    }

    public function storeScores(Request $request, string $subjectId, array $studentScores): void
    {
        $scores = $request->session()->get('teacher_scores', []);
        foreach ($studentScores as $studentId => $assessmentScores) {
            foreach ($assessmentScores as $assessmentId => $score) {
                $scores[$studentId.':'.$assessmentId] = ($score === null || $score === '') ? null : (int) $score;
            }
        }
        $request->session()->put('teacher_scores', $scores);
    }

    public function storeAnnouncement(Request $request, AnnouncementData $dto): void
    {
        $subject = $this->resolveSubject($request, $dto->subjectId);

        SubjectAnnouncement::create([
            'subject_id' => $subject['subject_id'],
            'section_subject_id' => $subject['section_subject_id'],
            'teacher_key' => $this->teacherKey($request),
            'teacher_name' => $request->session()->get('teacher_name', 'AMIS Teacher'),
            'teacher_email' => $request->session()->get('teacher_email'),
            'title' => $dto->title,
            'body' => $dto->body,
            'published_at' => $dto->date.' '.now()->format('H:i:s'),
        ]);
    }

    public function createClassAndChannels(Request $request, array $data): void
    {
        $grade = $data['grade'];
        $name = $data['name'] ?? null;
        $gender = $data['gender'];
        $mode = $data['mode'];
        $shift = $mode === 'Flexible Online Learning' ? ($data['shift'] ?? null) : null;
        $channels = $data['channels'] ?? [];

        $teacherName = $request->session()->get('teacher_name', 'AMIS Teacher');
        $teacherUpn = $request->session()->get('teacher_email');

        // Construct team name
        if ($grade === 'Kinder 1') {
            $prefix = 'K1';
        } elseif ($grade === 'Kinder 2') {
            $prefix = 'K2';
        } else {
            $prefix = 'G' . str_replace('Grade ', '', $grade);
        }

        $shiftLabel = $shift ? ($shift === '1st Shift' ? '1st Shift' : '2nd Shift') : 'F2F';
        $genderLabel = $gender === 'male' ? 'Boys' : 'Girls';
        $namePart = $name ? " - {$name}" : '';
        $teamName = "{$prefix}{$namePart} [{$genderLabel} & {$shiftLabel}]";

        $msTeamId = null;
        $msTeamUrl = null;

        try {
            $result = $this->graph->createTeam($teamName);
            $msTeamId = $result['id'];
            $msTeamUrl = "https://teams.microsoft.com/l/team/{$msTeamId}";

            // Wait for team to be ready
            $this->graph->waitForTeam($msTeamId);

            // Post welcome card to General channel
            $generalChannelId = $this->graph->getGeneralChannelId($msTeamId);
            if ($generalChannelId) {
                try {
                    $this->graph->postWelcomeCard($msTeamId, $generalChannelId, [
                        'grade_level'   => $grade,
                        'learning_mode' => $mode,
                        'shift'         => $shift,
                        'gender'        => $gender,
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Could not post welcome card to General channel: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to create MS Team [{$teamName}]: " . $e->getMessage());
            throw new \Exception('Failed to create Microsoft Team: ' . $e->getMessage());
        }

        // Save section in DB
        $schoolYear = config('services.school.year', '2026-2027');

        $section = Section::create([
            'name'          => $name,
            'grade_level'   => $grade,
            'learning_mode' => $mode,
            'shift'         => $shift,
            'gender'        => $gender,
            'school_year'   => $schoolYear,
            'ms_team_id'    => $msTeamId,
            'ms_team_url'   => $msTeamUrl,
        ]);

        // If teacher UPN is available, invite teacher as Team Owner
        if ($msTeamId && $teacherUpn) {
            try {
                $this->graph->addTeamOwner($msTeamId, $teacherUpn);
            } catch (\Exception $e) {
                Log::warning("Could not add teacher [{$teacherUpn}] as Team Owner: " . $e->getMessage());
            }
        }

        // Create subject/channels
        $adminUpn = config('services.microsoft.admin_upn');
        foreach ($channels as $channelName) {
            $channelId = null;
            if ($msTeamId) {
                try {
                    $channelResult = $this->graph->createPrivateChannel($msTeamId, $channelName, $adminUpn);
                    $channelId = $channelResult['id'] ?? null;

                    if ($channelId) {
                        // Post welcome card to private channel
                        try {
                            $this->graph->postWelcomeCard($msTeamId, $channelId, [
                                'grade_level'   => $grade,
                                'learning_mode' => $mode,
                                'shift'         => $shift,
                                'gender'        => $gender,
                                'subject'       => $channelName,
                                'teacher'       => $teacherName,
                            ]);
                        } catch (\Exception $e) {
                            Log::warning("Could not post welcome card to channel [{$channelName}]: " . $e->getMessage());
                        }

                        // Invite teacher as Channel Owner
                        if ($teacherUpn) {
                            try {
                                $this->graph->addChannelOwner($msTeamId, $channelId, $teacherUpn);
                            } catch (\Exception $e) {
                                Log::warning("Could not invite teacher [{$teacherUpn}] as channel owner: " . $e->getMessage());
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to create MS private channel [{$channelName}]: " . $e->getMessage());
                }
            }

            SectionSubject::create([
                'section_id'    => $section->id,
                'subject_name'  => $channelName,
                'teacher_name'  => $teacherName,
                'schedule'      => null,
                'ms_channel_id' => $channelId,
            ]);
        }
    }

    private function subjectsFor(Request $request): Collection
    {
        $teacherKey = $this->teacherKey($request);
        $teacherName = $request->session()->get('teacher_name');
        $teacherEmail = $request->session()->get('teacher_email');

        $assigned = TeacherSubjectAssignment::with('subject')
            ->where(function ($query) use ($teacherKey, $teacherEmail) {
                $query->where('teacher_key', $teacherKey);
                if ($teacherEmail) {
                    $query->orWhere('teacher_email', $teacherEmail);
                }
            })
            ->where('status', 'active')
            ->get();

        $sectionSubjects = SectionSubject::with('section')
            ->where(function ($query) use ($teacherName) {
                $query->where('teacher_name', $teacherName)
                    ->orWhere('teacher_name', 'like', '%'.trim((string) $teacherName).'%');
            })
            ->get();

        // Keep track of section subjects that we've already mapped via TeacherSubjectAssignment
        $mappedSectionSubjectIds = collect();

        $assignedSubjects = $assigned->flatMap(function (TeacherSubjectAssignment $assignment) use ($sectionSubjects, &$mappedSectionSubjectIds) {
            $matches = $sectionSubjects->filter(fn ($row) => Str::lower($row->subject_name) === Str::lower($assignment->subject?->name));
            if ($matches->isEmpty()) {
                return [$this->catalogSubjectArray($assignment->subject)];
            }

            return $matches->map(function (SectionSubject $sectionSubject) use ($assignment, &$mappedSectionSubjectIds) {
                $mappedSectionSubjectIds->push($sectionSubject->id);
                return $this->sectionSubjectArray($sectionSubject, $assignment->subject);
            });
        })->filter();

        // Find SectionSubjects where teacher matches, but they weren't matched in assignments
        $unmappedSectionSubjects = $sectionSubjects->reject(fn ($row) => $mappedSectionSubjectIds->contains($row->id));

        $directSubjects = $unmappedSectionSubjects->map(function (SectionSubject $sectionSubject) {
            $subjectName = $sectionSubject->subject_name;
            $gradeLevel = $sectionSubject->section?->grade_level;

            $catalogSubject = Subject::where('name', $subjectName)
                ->where('grade_level', $gradeLevel)
                ->first();

            if (!$catalogSubject) {
                $catalogSubject = Subject::where('name', $subjectName)->first();
            }

            return $this->sectionSubjectArray($sectionSubject, $catalogSubject);
        });

        return $assignedSubjects->concat($directSubjects)->unique('id')->values();
    }

    private function studentsFor(Collection $subjects): Collection
    {
        return \App\Models\StudentSection::with('student.user')
            ->whereIn('section_id', $subjects->pluck('section_id')->filter()->unique())
            ->get()
            ->map(fn ($row) => [
                'id' => 'stu-'.$row->student_id,
                'section_subject_id' => $subjects->firstWhere('section_id', $row->section_id)['section_subject_id'] ?? null,
                'name' => $row->student?->user?->name ?? 'Student '.$row->student_id,
                'student_no' => $row->student?->student_number ?? 'N/A',
                'grade' => $row->student?->grade_level ?? '',
                'section' => $row->student?->section ?? '',
            ]);
    }

    private function catalogSubjectArray(?Subject $subject): ?array
    {
        if (! $subject) return null;

        return [
            'id' => 'subject-'.$subject->id,
            'subject_id' => $subject->id,
            'section_subject_id' => null,
            'section_id' => null,
            'name' => $subject->name,
            'code' => $subject->code,
            'grade' => $subject->grade_level,
            'section' => 'Not linked to a class section',
            'schedule' => null,
            'room' => null,
            'mode' => 'Assigned',
            'advisor' => null,
        ];
    }

    private function sectionSubjectArray(SectionSubject $row, ?Subject $subject): array
    {
        return [
            'id' => 'section-subject-'.$row->id,
            'subject_id' => $subject?->id,
            'section_subject_id' => $row->id,
            'section_id' => $row->section_id,
            'name' => $row->subject_name,
            'code' => $subject?->code,
            'grade' => $row->section?->grade_level,
            'section' => $row->section?->section_title,
            'schedule' => $row->schedule,
            'room' => null,
            'mode' => $row->section?->learning_mode ?? 'F2F',
            'advisor' => $row->section?->grade_advisor?->teacher_name ?? null,
        ];
    }

    private function resolveSubject(Request $request, string $workspaceId): array
    {
        $subject = $this->subjectsFor($request)->firstWhere('id', $workspaceId); abort_unless($subject, 403, 'This subject is not assigned to you.');
        return $subject;
    }

    private function meetingArray(SubjectMeeting $meeting): array
    {
        $workspaceId = $meeting->section_subject_id ? 'section-subject-'.$meeting->section_subject_id : 'subject-'.$meeting->subject_id;

        return [
            'id' => 'meeting-'.$meeting->id,
            'subject_id' => $workspaceId,
            'title' => $meeting->title,
            'date' => optional($meeting->meeting_date)->toDateString(),
            'time' => substr((string) $meeting->meeting_time, 0, 5),
            'duration' => $meeting->duration_minutes,
            'link' => $meeting->meeting_url,
            'agenda' => $meeting->description,
            'status' => Str::headline($meeting->status),
        ];
    }

    private function materialArray(LearningMaterial $material): array
    {
        $workspaceId = $material->section_subject_id ? 'section-subject-'.$material->section_subject_id : 'subject-'.$material->subject_id;

        return [
            'id' => 'material-'.$material->id,
            'subject_id' => $workspaceId,
            'title' => $material->title,
            'description' => $material->description,
            'type' => $material->type,
            'url' => $material->external_url ?: Storage::disk($material->disk ?: 'public')->url($material->path),
            'created_at' => $material->created_at?->format('M d, Y'),
        ];
    }

    private function announcementArray(SubjectAnnouncement $announcement): array
    {
        $workspaceId = $announcement->section_subject_id ? 'section-subject-'.$announcement->section_subject_id : 'subject-'.$announcement->subject_id;

        return [
            'id' => 'announcement-'.$announcement->id,
            'subject_id' => $workspaceId,
            'title' => $announcement->title,
            'audience' => 'Assigned students',
            'date' => $announcement->published_at?->toDateString(),
            'body' => $announcement->body,
        ];
    }

    private function teacherKey(Request $request): string
    {
        return Str::slug($request->session()->get('teacher_name', 'teacher'));
    }

}
