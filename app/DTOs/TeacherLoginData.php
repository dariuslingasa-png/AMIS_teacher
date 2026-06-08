<?php

namespace App\DTOs;

readonly class TeacherLoginData
{
    public function __construct(
        public string $teacherId,
        public string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            teacherId: trim((string) ($data['teacher_id'] ?? '')),
            password: (string) ($data['password'] ?? '')
        );
    }
}
