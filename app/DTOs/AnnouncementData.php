<?php

namespace App\DTOs;

readonly class AnnouncementData
{
    public function __construct(
        public string $subjectId,
        public string $title,
        public string $audience,
        public string $date,
        public string $body
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            subjectId: (string) ($data['subject_id'] ?? ''),
            title: trim((string) ($data['title'] ?? '')),
            audience: trim((string) ($data['audience'] ?? 'Assigned subject')),
            date: (string) ($data['date'] ?? ''),
            body: trim((string) ($data['body'] ?? ''))
        );
    }

    public function toArray(): array
    {
        return [
            'subject_id' => $this->subjectId,
            'title' => $this->title,
            'audience' => $this->audience,
            'date' => $this->date,
            'body' => $this->body,
        ];
    }
}
