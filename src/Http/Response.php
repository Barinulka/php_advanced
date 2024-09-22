<?php

namespace App\Http;

use JsonException;

abstract class Response
{
    protected const SUCCESS = true;

    /**
     * @throws JsonException
     */
    public function send(): void
    {
        // Формат ответа
        $data = ['success' => static::SUCCESS] + $this->payload();

        // Устанавливаем заголовок
        header('Content-Type: application/json');

        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    abstract protected function payload(): array;
}