<?php

namespace App\Repository\UserRepository;

use App\Exception\InvalidArgumentException;
use App\Exception\UserException\UserNotFoundException;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\UUID;
use PDO;

class SqliteUserRepository implements UserRepositoryInterface
{

    public function __construct(
        private PDO $connection
    ){
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (first_name, last_name, uuid, username)
                    VALUES (:first_name, :last_name, :uuid, :username)'
        );

        $statement->execute([
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':uuid' => $user->getUuid(),
            ':username' => $user->getLogin()
        ]);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );
        $statement->execute([
            (string) $uuid
        ]);

        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username
        ]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getUser(\PDOStatement $statement, string $errorMessage): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Бросаем исключение, если пользователь не найден
        if (false === $result) {
            throw new UserNotFoundException(
                sprintf('Пользователь %s не найден!', (string) $errorMessage)
            );
        }

        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }
}