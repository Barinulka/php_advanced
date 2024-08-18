<?php

namespace App\Repository\UserRepository;

use App\Exception\InvalidArgumentException;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;

class DummyUserRepository implements UserRepositoryInterface
{

    public function save(User $user): void
    {
        
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException(
            sprintf('Пользователь %s не найден!', '')
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        return new User(
            UUID::random(),
            new Name('firstName', 'lastName'),
           'login'
        );
    }

}