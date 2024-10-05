<?php

namespace Actions\Users;

use App\Exception\UserException\UserNotFoundException;
use App\Http\Actions\Users\ActionFindByUsername;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\SuccessfulResponse;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use App\Repository\UserRepository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{

    /*
     * Тест, проверяющий, что будет возвращён неудачный ответ,
     * если в запросе нет параметра username
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        /*
         * Создаем объект запроса
         * с пустыми даннымими
        */
        $request = new Request([], [], '');

        $userRepository = $this->userRepository([]);

        $action = new ActionFindByUsername($userRepository);

        $response = $action->handle($request);

        // Проверяем что ответ неудачный
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

        $response->send();
    }

    /*
     * Тест, проверяющий, что будет возвращён неудачный ответ,
     * если пользователь не найден
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['username' => 'test'], [], '');

        $userRepository = $this->userRepository([]);

        $action = new ActionFindByUsername($userRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"User Not found"}');

        $response->send();
    }

    /*
     * Тест, проверяющий, что будет возвращён успешный ответ
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username'=>'test'], [], '');

        $usersRepository = $this->userRepository([
            new User(
                UUID::random(),
                new Name('test', 'test'),
                'test'
            )
        ]);

        $action = new ActionFindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString('{"success":true,"data":{"username":"test","name":"test test"}}');

        $response->send();
    }

    private function userRepository(array $users): UserRepositoryInterface
    {
        return new class($users) implements UserRepositoryInterface {

            public function __construct(
                private array $users
            ) {
            }

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException('User Not found');
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getLogin()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException('User Not found');
            }
        };
    }

}