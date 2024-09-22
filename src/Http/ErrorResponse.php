<?php

namespace App\Http;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;

    public function __construct(
        private string $reason = 'Что-то пошло не так'
    )   {
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}