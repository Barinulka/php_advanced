<?php

namespace Repository\PostRepository;

use App\Exception\PostException\PostNotFoundException;
use App\Model\Blog\Post;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use App\Repository\PostRepository\SqlitePostRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
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
        $repository = new SqlitePostRepository($connectionStub);
        // Ожидаем, что будет брошено исключение
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Статья %s не найдена', '1bbf7afc-ef85-4f74-be21-248405ed8b77'));

        $repository->get(new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'));
    }

    public function testItReturnPostByUUID(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        // Стаб запроса будет отдавать подготовленные данные
        $statementMock->method('fetch')->willReturn(
            [
                'uuid' => '1bbf7afc-ef85-4f74-be21-248405ed8b77',
                'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
                'text' => 'Some Post Text',
                'title' => 'Some Post Title',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
                'username' => 'ivan123'
            ]
        );

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostRepository($connectionStub);

        $result = $repository->get(new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'));

        $this->assertInstanceOf(Post::class, $result);
        $this->assertSame('1bbf7afc-ef85-4f74-be21-248405ed8b77', (string) $result->getUuid());
    }

    public function testItSavePostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        //  Ожидаемое взаимодействие репозитория с моком запроса
        $statementMock
            ->expects($this->once())        // Ожидаем, чтобудет вызван один раз
            ->method('execute')    // метод execute
            ->with([                        // с единственным аргументом - массивом
                ':uuid' => '1bbf7afc-ef85-4f74-be21-248405ed8b77',
                ':author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
                ':title' => 'Some Post Title',
                ':text' => 'Some Post Text',
            ])
        ;

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostRepository($connectionStub);

        // Создаем тестового автора
        $user = new User(
            new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
            new Name('FirstName', 'LastName'),
            'UserLogin'
        );

        $repository->save(
            new Post(
                new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'),
                $user,
                'Some Post Title',
                'Some Post Text'
            )
        );
    }


}