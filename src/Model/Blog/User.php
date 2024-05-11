<?php

namespace App\Model\Blog;

use App\Model\Person\Name;
use App\Model\UUID;

class User
{
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private string $login
    )
    {
    }

    public function __toString(): string
    {
        return "Пользователь $this->uuid с именем $this->name и логином $this->login." . PHP_EOL;
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setUsername(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

}