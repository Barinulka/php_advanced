<?php

namespace App\Repository\UserRepository;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;
use App\Model\UUID;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        $return = null;

        foreach ($this->users as $user) {
            if ($user->getUUID() === (string) $uuid) {
                $return = $user;
            }
        }

        if (null === $return) {
            throw new UserNotFoundException(
                sprintf('Пользователь %s не найден!', (string) $uuid)
            );
        }

        return $return;

    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }

        throw new UserNotFoundException(
            sprintf('Пользователь %s не найден!', (string) $username)
        );
    }

}