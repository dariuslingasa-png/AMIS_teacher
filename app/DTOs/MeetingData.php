<?php

namespace App\DTOs;

use App\Enums\MeetingStatus;

readonly class MeetingData
{
    public function __construct(
        public string $subjectId,
        public string $title,
        public string $date,
        public string $time,
        public int $duration,
        public ?string $link,
        public ?string $description,
        public MeetingStatus $status
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            subjectId: (string) ($data['subject_id'] ?? ''),
            title: trim((string) ($data['title'] ?? '')),
            date: (string) ($data['date'] ?? ''),
            time: (string) ($data['time'] ?? ''),
            duration: (int) ($data['duration'] ?? 60),
            link: isset($data['link']) ? trim((string) $data['link']) : null,
            description: trim((string) ($data['description'] ?? $data['agenda'] ?? '')),
            status: MeetingStatus::from($data['status'] ?? 'Scheduled')
        );
    }

    public function toArray(): array
    {
        return [
            'subject_id' => $this->subjectId,
            'title' => $this->title,
            'date' => $this->date,
            'time' => $this->time,
            'duration' => $this->duration,
            'link' => $this->link,
            'description' => $this->description,
            'agenda' => $this->description,
            'status' => $this->status->value,
        ];
    }
}
