<?php

namespace Repository\UserRepository;

use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use App\Repository\UserRepository\SqliteUserRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/*
 * Класс для освоения стабов и моков
 */
class SqliteUsersRepositoryTest extends TestCase
{
    // Тест, проверяющий, что SQLite-репозиторий бросает исключение,
    // когда запрашиваемый пользователь не найден
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        // Подготавливаем все необходимые стабы

        // 1. Стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // 2. Стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);

        // 3. Стаб запроса будет отдавать false
        // при вызове метода fetch
        $statementStub->method('fetch')->willReturn(false);

        // 4. Стаб подключения возвращает стаб запроса
        $connectionStub->method('prepare')->willReturn($statementStub);

        // Передаем в репозиторий стаб подключения
        $repository = new SqliteUserRepository($connectionStub);

        // Ожидаем, что будет брошено исключение
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Пользователь Ivan не найден!');

        $repository->getByUsername('Ivan');
    }

    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesUserToDatabase(): void
    {
        // 1. Стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // 2. Создаём мок запроса, возвращаемый стабомподключения
        $statementMock = $this->createMock(PDOStatement::class);

        // 3. Описываем ожидаемое взаимодействие нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once())        // Ожидаем, чтобудет вызван один раз
            ->method('execute')    // метод execute
            ->with([                        // с единственным аргументом - массивом
                ':uuid'=>'123e4567-e89b-12d3-a456-426614174000',
                ':username'=>'ivan123',
                ':first_name'=>'Ivan',
                ':last_name'=>'Nikitin'
            ]);

        // 4. При вызове метода prepare стаб подключения вернет мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

        // Передаем в репозиторий стаб подключения
        $repository = new SqliteUserRepository($connectionStub);

        // Вызываем метод сохранения пользователя
        $repository->save(
            new User(
                // Свойста пользователя точно такие же, как и в моке
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123'
            )
        );
    }

    // Тест проверяет, что Sqlite репозиторий отдает пользоваателя
    public function testItReturnUserByUsername(): void
    {
        // Подготавливаем все необходимые стабы

        // 1. Стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // 2. Стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);

        // 3. Стаб запроса будет отдавать false
        // при вызове метода fetch
        $statementStub->method('fetch')->willReturn(
            [
                'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
                'username' => 'ivan123'
            ]
        );

        // 4. Стаб подключения возвращает стаб запроса
        $connectionStub->method('prepare')->willReturn($statementStub);

        // Передаем в репозиторий стаб подключения
        $repository = new SqliteUserRepository($connectionStub);

        $result = $repository->getByUsername('ivan123');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $result->getUuid());
        $this->assertEquals('Ivan', $result->getName()->getFirstName());
        $this->assertEquals('Nikitin', $result->getName()->getLastName());
        $this->assertEquals('ivan123', $result->getLogin());
    }

    public function testItReturnUserByUUID(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementSub = $this->createStub(PDOStatement::class);

        $statementSub->method('fetch')->willReturn( [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'username' => 'ivan123'
        ]);

        $connectionStub->method('prepare')->willReturn($statementSub);

        $repository = new SqliteUserRepository($connectionStub);

        $result = $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $result->getUuid());
    }
}