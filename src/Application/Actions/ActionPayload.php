<?php

declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

readonly class ActionPayload implements JsonSerializable
{
    public function __construct(
        private int $statusCode = 200,
        private array|null|object $data = null,
        private ?ActionError $error = null,
        private ?array $errors = null
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): object|array|null
    {
        return $this->data;
    }

    public function getError(): ?ActionError
    {
        return $this->error;
    }

    public function jsonSerialize(): array
    {
        $payload = [
            'statusCode' => $this->statusCode,
        ];

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        } elseif ($this->errors !== null) {
            $payload['errors'] = $this->errors;
        }

        return $payload;
    }
}
