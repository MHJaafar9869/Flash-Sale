<?php

declare(strict_types=1);

namespace App\DTOs;

class ResponseDto
{
    public function __construct(
        public readonly bool $success,
        public readonly int $status,
        public readonly string $message,
        public readonly mixed $data = null,
    ) {}

    public static function success(
        int $statusCode = 200,
        string $message = 'Request successful',
        mixed $data = null,
    ): self {
        return new self(
            success: true,
            message: $message,
            status: $statusCode,
            data: $data
        );
    }

    public static function error(
        int $statusCode = 400,
        string $message = 'Request failed',
        mixed $data = null,
    ): self {
        return new self(
            success: false,
            status: $statusCode,
            message: $message,
            data: $data
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            status: $data['status'] ?? 400,
            message: $data['message'] ?? '',
            data: $data['data'],
        );
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setStatus(int $status): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            data: $this->data,
            status: $status,
        );
    }

    public function setMessage(string $message): self
    {
        return new self(
            success: $this->success,
            message: $message,
            data: $this->data,
            status: $this->status,
        );
    }

    public function setData(mixed $data): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            data: $data,
            status: $this->status,
        );
    }
}
