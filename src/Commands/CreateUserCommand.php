<?php

namespace App\Commands;

use App\Exception\ArgumentsException;
use App\Exception\CommandException;
use App\Exception\InvalidArgumentException;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use App\Repository\UserRepository\UserRepositoryInterface;

readonly class CreateUserCommand
{

    public function __construct(
        private UserRepositoryInterface $userRepository
    ){
    }

    /**
     * @throws InvalidArgumentException
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $userName = $arguments->get('username');

        if ($this->userExist($userName)) {
            throw new CommandException(
                sprintf('Пользователь %s уже существует', $userName)
            );
        }

        $this->userRepository->save(
           new User(
               UUID::random(),
               new Name(
                   $arguments->get('first_name'),
                   $arguments->get('last_name')
               ),
               $userName
           )
        );
    }

    private function userExist(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }

}