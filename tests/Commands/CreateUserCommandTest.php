<?php

namespace App\UnitTests\Commands;

use App\Commands\Arguments;
use App\Commands\CreateUserCommand;
use App\Exception\ArgumentsException;
use App\Exception\CommandException;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;
use App\Model\UUID;
use App\Repository\UserRepository\DummyUserRepository;
use App\Repository\UserRepository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUserRepository());

        $this->expectException(CommandException::class);

        $this->expectExceptionMessage(sprintf('Пользователь %s уже существует', 'Иван'));

        $command->handle(new Arguments(['username' => 'Иван']));

    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("Не передан аргумент: last_name");

        $command->handle(new Arguments([
            'username' => 'Иван',
            'first_name' => 'Иванович'
        ]));
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("Не передан аргумент: first_name");

        $command->handle(new Arguments(['username' => 'Иван']));
    }

    // Пример создания стаба
    private function makeUserRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    // Пример исользования мока
    // Тест, проверяющий, что команда сохраняет пользователяв репозитории
    public function testItSavesUserToRepository():void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UserRepositoryInterface {
            // В этом свойстве мы храним информациюо том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository);

        $command->handle(new Arguments([
            'username' => 'ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov'
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }

    public function testItThrowWhenCreateUserWithEmptyFromArgv(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("Не передан аргумент: username");

        $command->handle(Arguments::fromArgv([]));
    }

    public function testItCreateUserFromArgv(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UserRepositoryInterface {
            // В этом свойстве мы храним информациюо том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository);

        $command->handle(Arguments::fromArgv([
            'username=test',
            'first_name=test',
            'last_name=test'
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }

    public function testItThrowErrorWhenCreateUserFromArgv(): void
    {
        $command = new CreateUserCommand($this->makeUserRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("Не передан аргумент: username");

        $command->handle(Arguments::fromArgv([
            'username'
        ]));
    }
}