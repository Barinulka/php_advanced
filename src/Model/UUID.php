<?php

namespace App\Model;


use App\Exception\InvalidArgumentException;

readonly class UUID
{

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $uuidString
    ){
        // Бросаем исключение, если UUID неправильного формата
        if (!uuid_is_valid($this->uuidString)) {
            throw new InvalidArgumentException(
                sprintf('Неверный UUID: %s', $this->uuidString)
            );
        }
    }

    // Генерируем случайный UUID

    /**
     * @throws InvalidArgumentException
     */
    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }
}