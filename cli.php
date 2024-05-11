<?php

require_once 'functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Commands\Arguments;
use App\Commands\CreateUserCommand;
use App\Exception\AppException;
use App\Model\Blog\Post;
use App\Model\Blog\Comment;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\Person\Person;
use App\Model\UUID;
use App\Repository\PostRepository\SqlitePostRepository;
use App\Repository\UserRepository\InMemoryUserRepository;
use App\Repository\UserRepository\SqliteUserRepository;

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');

$userRepository = new SqliteUserRepository($connection);

$userCommand = new CreateUserCommand($userRepository);

try {
    $userCommand->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo 'Что-то пошло не так' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}

