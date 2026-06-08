<?php

namespace App\DTOs;

use App\Enums\LearningMode;

readonly class SubjectData
{
    public function __construct(
        public string $name,
        public ?string $code,
        public string $grade,
        public string $section,
        public ?string $schedule,
        public ?string $room,
        public LearningMode $mode
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) ($data['name'] ?? '')),
            code: isset($data['code']) ? trim((string) $data['code']) : null,
            grade: trim((string) ($data['grade'] ?? '')),
            section: trim((string) ($data['section'] ?? '')),
            schedule: isset($data['schedule']) ? trim((string) $data['schedule']) : null,
            room: isset($data['room']) ? trim((string) $data['room']) : null,
            mode: LearningMode::from($data['mode'] ?? 'F2F')
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'grade' => $this->grade,
            'section' => $this->section,
            'schedule' => $this->schedule,
            'room' => $this->room,
            'mode' => $this->mode->value,
        ];
    }
}
