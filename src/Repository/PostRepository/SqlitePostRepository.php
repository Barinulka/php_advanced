<?php

namespace App\Repository\PostRepository;

use App\Exception\InvalidArgumentException;
use App\Exception\PostException\PostNotFoundException;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\Post;
use App\Model\UUID;
use App\Repository\UserRepository\SqliteUserRepository;
use PDO;

class SqlitePostRepository implements PostRepositoryInterface
{

    public function __construct(
        private PDO $connection
    ){
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) 
                    VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':author_uuid' => (string)$post->getUser()->getUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()
        ]);
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException|UserNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $message): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new PostNotFoundException(
                sprintf('Статья %s не найдена', $message)
            );
        }

        $userRepository = new SqliteUserRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );

    }
}