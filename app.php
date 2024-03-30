<?php

require_once 'functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Model\Blog\Post;
use App\Model\Blog\Comment;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\Person\Person;
use App\Repository\UserRepository\InMemoryUserRepository;

$faker = Faker\Factory::create('ru_RU');

$name = new Name($faker->firstName('male'), $faker->lastName('male'));
$user = new User(1, $name, $faker->userName());
$person = new Person($name, new DateTimeImmutable());

$command = $argv[1] ?? null;

switch ($command) {
    case 'user':
        echo $user;
        break;
    case 'post':
        $post = new Post(
            $faker->randomDigitNotNull(),
            $user,
            $faker->realText(rand(10,100))
        );
        echo $post;
        break;
    case 'comment':
        $post = new Post(
            $faker->randomDigitNotNull(),
            $user,
            $faker->realText(rand(10,100))
        );
        $comment = new Comment(
            $faker->randomDigitNotNull(),
            $user,
            $post,
            $faker->realText(rand(10,50))
        );
        echo $comment;
        break;
    default:
        echo 'Введите одну из перечисленных команд:' . PHP_EOL;
        echo "'user', 'post', 'comment'" . PHP_EOL;
}

