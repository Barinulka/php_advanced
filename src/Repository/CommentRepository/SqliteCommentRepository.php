<?php

namespace App\Repository\CommentRepository;

use App\Exception\CommentException\CommentNotFoundException;
use App\Exception\InvalidArgumentException;
use App\Exception\PostException\PostNotFoundException;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\Comment;
use App\Model\UUID;
use App\Repository\PostRepository\SqlitePostRepository;
use App\Repository\UserRepository\SqliteUserRepository;
use PDO;

class SqliteCommentRepository implements CommentRepositoryInterface
{

    public function __construct(
        private PDO $connection
    ){
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
                    VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->getUuid(),
            ':post_uuid' => (string)$comment->getPost()->getUuid(),
            ':author_uuid' => (string)$comment->getUser()->getUuid(),
            ':text' => $comment->getText()
        ]);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     * @throws CommentNotFoundException
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string) $uuid
        ]);

        return $this->getComment($statement, $uuid);
    }

    /**
     * @param \PDOStatement $statement
     * @param string $message
     * @return Comment
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     */
    private function getComment(\PDOStatement $statement, string $message): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new CommentNotFoundException(
                sprintf('Комментарий %s не найден', $message)
            );
        }

        $userRepository = new SqliteUserRepository($this->connection);
        $postRepository = new SqlitePostRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));
        $post = $postRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['text']
        );
    }
}