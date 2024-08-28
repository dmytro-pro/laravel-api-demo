<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class SubmissionDto implements Arrayable
{
    public function __construct(
        public string $name,
        public string $email,
        public string $message,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ];
    }
}
