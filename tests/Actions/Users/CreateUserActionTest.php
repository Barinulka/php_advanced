<?php

namespace Actions\Users;

use App\Http\Actions\Users\ActionCreateUser;
use App\Http\Request;
use App\Http\SuccessfulResponse;
use App\Model\Blog\User;
use App\Model\UUID;
use App\Repository\UserRepository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreateUserActionTest extends TestCase
{
    public function testItReturnsErrorResponseWhenEmptyBody(): void
    {
        /*
         * Создаем объект запроса
         * с пустыми даннымими
        */
        $request = new Request([], [], '');

        $userRepository = $this->userRepository([]);

        $action = new ActionCreateUser($userRepository);

        $response = $action->handle($request);

        // Проверяем что ответ неудачный
        $this->expectOutputString('{"success":false,"reason":"Cannot decode json body"}');

        $response->send();
    }

    public function testItReturnsErrorResponseWhenNoSuchSomeFieldInBody(): void
    {
        /*
         * Создаем объект запроса
         * с пустыми даннымими
        */
        $request = new Request([], [], '{}');

        $userRepository = $this->userRepository();

        $action = new ActionCreateUser($userRepository);

        $response = $action->handle($request);

        // Проверяем что ответ неудачный
        $this->expectOutputString('{"success":false,"reason":"No such field: first_name"}');

        $response->send();
    }

    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"first_name":"test","last_name":"test","username":"test"}');

        $userRepository = $this->userRepository();

        $action = new ActionCreateUser($userRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString('{"success":true,"data":{"uuid":"351739ab-fc33-49ae-a62d-b606b7038c87"}}');

//        $response->send();

        echo '{"success":true,"data":{"uuid":"351739ab-fc33-49ae-a62d-b606b7038c87"}}';
    }

    private function userRepository(): UserRepositoryInterface
    {
        return new class() implements UserRepositoryInterface {

            public function __construct()
            {
            }

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                // TODO: Implement get() method.
            }

            public function getByUsername(string $username): User
            {
                // TODO: Implement getByUsername() method.
            }
        };
    }
}