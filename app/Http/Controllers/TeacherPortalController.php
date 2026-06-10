<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\StoreScoresRequest;
use App\Http\Requests\StoreAnnouncementRequest;
use App\DTOs\MeetingData;
use App\DTOs\AssessmentData;
use App\DTOs\AnnouncementData;
use App\Services\TeacherPortalService;
use Illuminate\Http\Request;

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
        $data = $this->portalService->getPortalData($request);

        return view('teacher.subjects', [
            'subjects' => collect($data['subjects']),
            'meetings' => collect($data['meetings']),
            'materials' => collect($data['materials']),
            'students' => collect($data['students']),
            'announcements' => collect($data['announcements']),
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
        $data = $this->portalService->getPortalData($request);

        return view('teacher.meetings', [
            'subjects' => collect($data['subjects']),
            'meetings' => collect($data['meetings']),
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
        $data = $this->portalService->getPortalData($request);
        $subjects = collect($data['subjects']);
        $selectedSubjectId = $request->query('subject', $subjects->first()['id'] ?? null);

        return view('teacher.grades', [
            'subjects' => $subjects,
            'selectedSubjectId' => $selectedSubjectId,
            'selectedSubject' => $subjects->firstWhere('id', $selectedSubjectId),
            'assessments' => collect($data['assessments'])->where('subject_id', $selectedSubjectId)->values(),
            'students' => collect($data['students']),
            'scores' => $data['scores'],
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

        return view('teacher.students', [
            'students' => collect($data['students']),
            'subjects' => collect($data['subjects']),
        ]);
    }

    public function announcements(Request $request)
    {
        $data = $this->portalService->getPortalData($request);

        return view('teacher.announcements', [
            'announcements' => collect($data['announcements']),
            'subjects' => collect($data['subjects']),
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
        $user = \App\Models\User::where('email', session('teacher_email'))->first();
        if (!$user) {
            abort(404, 'User not found.');
        }

        return view('teacher.settings', [
            'user' => $user,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = \App\Models\User::where('email', session('teacher_email'))->first();
        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
