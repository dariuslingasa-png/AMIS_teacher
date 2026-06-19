<?php

namespace App\Http\Controllers;

use App\DTOs\AnnouncementData;
use App\DTOs\AssessmentData;
use App\DTOs\MeetingData;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\StoreScoresRequest;
use App\Http\Requests\StoreSubjectRequest;
use App\Models\User;
use App\Services\TeacherPortalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherPortalController extends Controller
{
    protected TeacherPortalService $portalService;

    public function __construct(TeacherPortalService $portalService)
    {
        $this->portalService = $portalService;
    }

    public function dashboard(Request $request)
    {
        $data = $this->portalService->getPortalData($request);

        return view('teacher.dashboard', [
            'data' => $data,
            'subjects' => collect($data['subjects']),
            'meetings' => collect($data['meetings']),
            'assessments' => collect($data['assessments']),
            'students' => collect($data['students']),
            'announcements' => collect($data['announcements']),
        ]);
    }

    public function subjects(Request $request)
    {
        return view('teacher.coming-soon', [
            'heading' => 'Classroom Workspace',
            'icon' => 'book-open',
        ]);
    }

    public function subjectWorkspace(Request $request, string $subject)
    {
        return view('teacher.subject-workspace', $this->portalService->workspace($request, $subject));
    }

    public function storeSubject(StoreSubjectRequest $request)
    {
        try {
            $this->portalService->createClassAndChannels($request, $request->validated());

            return redirect()->route('teacher.subjects')->with('success', 'Class and Channels successfully created and provisioned on MS Teams!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function meetings(Request $request)
    {
        return view('teacher.coming-soon', [
            'heading' => 'Meetings',
            'icon' => 'video',
        ]);
    }

    public function storeMeeting(StoreMeetingRequest $request)
    {
        $dto = MeetingData::fromArray($request->validated());
        $this->portalService->storeMeeting($request, $dto);

        return redirect()->route('teacher.meetings')->with('success', 'Meeting saved.');
    }

    public function storeMaterial(StoreMaterialRequest $request)
    {
        $this->portalService->storeMaterial($request, $request->validated());

        return back()->with('success', 'Learning material published.');
    }

    public function grades(Request $request)
    {
        return view('teacher.coming-soon', [
            'heading' => 'Gradebook',
            'icon' => 'clipboard-list',
        ]);
    }

    public function storeAssessment(StoreAssessmentRequest $request)
    {
        $dto = AssessmentData::fromArray($request->validated());
        $this->portalService->storeAssessment($request, $dto);

        return redirect()->route('teacher.grades', ['subject' => $dto->subjectId])->with('success', 'Assessment added.');
    }

    public function storeScores(StoreScoresRequest $request)
    {
        $validated = $request->validated();
        $this->portalService->storeScores($request, $validated['subject_id'], $validated['scores'] ?? []);

        return redirect()->route('teacher.grades', ['subject' => $validated['subject_id']])->with('success', 'Scores saved.');
    }

    public function students(Request $request)
    {
        $data = $this->portalService->getPortalData($request);
        $advisorySectionIds = $this->portalService->advisorySectionsFor($request);

        // Get actual Section models for advisory sections
        $advisorySections = \App\Models\Section::whereIn('id', $advisorySectionIds)->get()->map(function ($section) use ($data) {
            return [
                'title' => 'Advisory: ' . $section->section_title . ' (' . $section->learning_mode . ')',
                'students' => collect($data['students'])->where('section_id', $section->id)->values(),
            ];
        })->filter(fn($sec) => $sec['students']->isNotEmpty());

        // Get subjects sections
        $subjectSections = collect($data['subjects'])->map(function ($subject) use ($data) {
            $sectionTitle = str_replace($subject['grade'] . ' - ', '', $subject['section'] ?? '');
            $title = 'Subject: ' . $subject['grade'] . ' - ' . $subject['name'];
            if (!empty($sectionTitle)) {
                $title .= ' (' . $sectionTitle . ')';
            }
            return [
                'title' => $title,
                'students' => collect($data['students'])->where('section_id', $subject['section_id'])->values(),
            ];
        })->filter(fn($sec) => $sec['students']->isNotEmpty());

        return view('teacher.students', [
            'advisorySections' => $advisorySections,
            'subjectSections' => $subjectSections,
            'totalStudentsCount' => collect($data['students'])->unique('id')->count(),
        ]);
    }

    public function announcements(Request $request)
    {
        return view('teacher.coming-soon', [
            'heading' => 'Announcements',
            'icon' => 'megaphone',
        ]);
    }

    public function storeAnnouncement(StoreAnnouncementRequest $request)
    {
        $dto = AnnouncementData::fromArray($request->validated());
        $this->portalService->storeAnnouncement($request, $dto);

        return redirect()->route('teacher.announcements')->with('success', 'Announcement posted.');
    }

    public function settings(Request $request)
    {
        return view('teacher.coming-soon', [
            'heading' => 'Settings',
            'icon' => 'settings',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', session('teacher_email'))->first();
        if (! $user || ! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function ebook(Request $request)
    {
        return view('teacher.coming-soon', [
            'heading' => 'eBook',
            'icon' => 'book-open-check',
        ]);
    }
}
