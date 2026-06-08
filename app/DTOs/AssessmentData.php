<?php

namespace App\DTOs;

readonly class AssessmentData
{
    public function __construct(
        public string $subjectId,
        public string $title,
        public int $maxScore,
        public string $date
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            subjectId: (string) ($data['subject_id'] ?? ''),
            title: trim((string) ($data['title'] ?? '')),
            maxScore: (int) ($data['max_score'] ?? 0),
            date: (string) ($data['date'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'subject_id' => $this->subjectId,
            'title' => $this->title,
            'max_score' => $this->maxScore,
            'date' => $this->date,
        ];
    }
}
