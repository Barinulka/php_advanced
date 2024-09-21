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
use App\Repository\CommentRepository\SqliteCommentRepository;
use App\Repository\PostRepository\SqlitePostRepository;
use App\Repository\UserRepository\InMemoryUserRepository;
use App\Repository\UserRepository\SqliteUserRepository;

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');

$userRepository = new SqliteUserRepository($connection);
$postRepository = new SqlitePostRepository($connection);
$commentRepository = new SqliteCommentRepository($connection);

$faker = Faker\Factory::create('ru_RU');

$userCommand = new CreateUserCommand($userRepository);

try {
    $userCommand->handle(Arguments::fromArgv($argv));
    // $user = $userRepository->getByUsername('admin');
    // $post = $postRepository->get(new UUID('1bbf7afc-ef85-4f74-be21-248405ed8b77'));

    // $commentUuid = UUID::random();

    // $comment = new Comment(
    //     $commentUuid,
    //     $user,
    //     $post,
    //     $faker->realText(rand(10, 50))
    // );

    // echo $comment;
} catch (AppException $e) {
    echo 'Что-то пошло не так' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}

