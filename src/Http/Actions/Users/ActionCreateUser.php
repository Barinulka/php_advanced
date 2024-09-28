<?php

namespace App\Http\Actions\Users;

use App\Exception\Http\HttpException;
use App\Http\Actions\ActionInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use App\Repository\UserRepository\UserRepositoryInterface;

class ActionCreateUser implements ActionInterface
{

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUUID = UUID::random();

            $user = new User(
                $newUserUUID,
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                ),
                $request->jsonBodyField('username')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->userRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string) $newUserUUID
        ]);
    }
}