<?php

namespace App\Model\Blog;

use App\Model\Person\Name;

class User
{
    public function __construct(
        private int    $id,
        private Name $username,
        private string $login
    )
    {
    }

    public function __toString(): string
    {
        return "Пользователь $this->id с именем $this->username и логином $this->login." . PHP_EOL;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param Name $username
     */
    public function setUsername(Name $username): void
    {
        $this->username = $username;
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