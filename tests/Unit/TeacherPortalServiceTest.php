<?php

namespace Tests\Unit;

use App\DTOs\MeetingData;
use App\DTOs\SubjectData;
use App\Enums\LearningMode;
use App\Enums\MeetingStatus;
use App\Services\MicrosoftGraphService;
use App\Services\TeacherPortalService;
use PHPUnit\Framework\TestCase;

class TeacherPortalServiceTest extends TestCase
{
    public function test_portal_service_exposes_subject_workspace_actions(): void
    {
        $graphMock = $this->createMock(MicrosoftGraphService::class);
        $service = new TeacherPortalService($graphMock);

        $this->assertTrue(method_exists($service, 'getPortalData'));
        $this->assertTrue(method_exists($service, 'workspace'));
        $this->assertTrue(method_exists($service, 'storeMeeting'));
        $this->assertTrue(method_exists($service, 'storeMaterial'));
        $this->assertTrue(method_exists($service, 'storeAnnouncement'));
    }

    public function test_subject_data_dto_parsing(): void
    {
        $data = [
            'name' => 'Math II',
            'code' => 'MTH-02',
            'grade' => 'Grade 8',
            'section' => 'A',
            'schedule' => 'Mon 9:00 AM',
            'room' => '101',
            'mode' => 'Hybrid',
        ];

        $dto = SubjectData::fromArray($data);

        $this->assertEquals('Math II', $dto->name);
        $this->assertEquals('MTH-02', $dto->code);
        $this->assertEquals(LearningMode::HYBRID, $dto->mode);

        $mapped = $dto->toArray();
        $this->assertEquals('Hybrid', $mapped['mode']);
    }

    public function test_meeting_data_dto_parsing(): void
    {
        $data = [
            'subject_id' => 'subj-1',
            'title' => 'Project Review',
            'date' => '2026-06-08',
            'time' => '14:30',
            'link' => 'https://teams.microsoft.com/test',
            'agenda' => 'Review milestones',
            'status' => 'Scheduled',
        ];

        $dto = MeetingData::fromArray($data);

        $this->assertEquals('subj-1', $dto->subjectId);
        $this->assertEquals('Project Review', $dto->title);
        $this->assertEquals(MeetingStatus::SCHEDULED, $dto->status);

        $mapped = $dto->toArray();
        $this->assertEquals('Scheduled', $mapped['status']);
    }
}
