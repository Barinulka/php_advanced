<?php

namespace Actions;

use App\Exception\UserException\UserNotFoundException;
use App\Http\Actions\Users\ActionFindByUsername;
use App\Http\Request;
use App\Model\Blog\User;
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
        $request = new Request([], []);

        $userRepository = $this->userRepository([]);

        $action = new ActionFindByUsername($userRepository);

        $response = $action->handle($request);

        // Проверяем что ответ неудачный
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

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
                throw new UserNotFoundException('Пользователь не найден');
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getLogin()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException('Пользователь не найден');
            }
        };
    }

}