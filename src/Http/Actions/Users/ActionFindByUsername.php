<?php

namespace App\Http\Actions\Users;

use App\Exception\Http\HttpException;
use App\Exception\UserException\UserNotFoundException;
use App\Http\Actions\ActionInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Repository\UserRepository\UserRepositoryInterface;

class ActionFindByUsername implements ActionInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Пытаемся получить искомое имя пользователя из запроса
        try {
            $username = $request->query('username');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'username' => $user->getLogin(),
            'name' => $user->getName()->getFirstName() . ' ' . $user->getName()->getLastName()
        ]);
    }
}