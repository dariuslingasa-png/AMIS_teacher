<?php

namespace App\DTOs;

readonly class TeacherChangePasswordData
{
    public function __construct(
        public string $email,
        public string $tempPassword,
        public string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: trim((string) ($data['email'] ?? '')),
            tempPassword: (string) ($data['temp_password'] ?? ''),
            password: (string) ($data['password'] ?? '')
        );
    }
}
