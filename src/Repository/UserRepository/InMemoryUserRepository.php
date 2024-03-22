<?php

namespace App\Repository\UserRepository;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;

class InMemoryUserRepository
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function get(int $id): User
    {
        $return = null;

        foreach ($this->users as $user) {
            if ($user->getId() == $id) {
                $return = $user;
            }
        }

        if (null === $return) {
            throw new UserNotFoundException("Пользователь {$id} не найден!");
        }

        return $return;

    }

}