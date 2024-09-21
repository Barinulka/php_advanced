<?php

namespace Repository\CommentRepository;

use App\Exception\CommentException\CommentNotFoundException;
use App\Model\Blog\Comment;
use App\Model\Blog\Post;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use App\Repository\CommentRepository\SqliteCommentRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
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
        $repository = new SqliteCommentRepository($connectionStub);
        // Ожидаем, что будет брошено исключение
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Комментарий %s не найден', '1bbf7afc-ef85-4f74-be21-248405ed8b77'));

        $repository->get(new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'));
    }

    public function testItReturnPostByUUID(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementCommentStub = $this->createStub(PDOStatement::class);

        $statementCommentStub->method('fetch')->willReturn(
            [
                'uuid' => '1bbf7afc-ef85-4f74-be21-248405ed8b77',
                'post_uuid' => '10bf7afc-ef85-4f74-be21-248405ed8b77',
                'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
                'text' => 'text',
                'title' => 'Some Post Title',
                'first_name' => 'name',
                'last_name' => 'last',
                'username' => 'username'
            ]
        );

        $connectionStub->method('prepare')->willReturn($statementCommentStub);
        $repository = new SqliteCommentRepository($connectionStub);

        $result = $repository->get(new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'));

        $this->assertInstanceOf(Comment::class, $result);
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
                ':post_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
                ':author_uuid' => '5e52ed7a-0ae4-495f-b666-c52bc8f13fe4',
                ':text' => 'Some Text',
            ])
        ;

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentRepository($connectionStub);

        // Создаем тестового автора
        $user = new User(
            new UUID('5e52ed7a-0ae4-495f-b666-c52bc8f13fe4'),
            new Name('FirstName', 'LastName'),
            'UserLogin'
        );

        $post = new Post(
            new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
            $user,
            'Some Post Title',
            'Some Post Text'
        );

        $repository->save(
            new Comment(
                new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'),
                $user,
                $post,
                'Some Text'
            )
        );
    }
}