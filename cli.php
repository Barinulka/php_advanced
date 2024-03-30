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
$post = new Post(1, $person, $faker->realText(rand(50,100)));
$comment = new Comment(1, $user, $post, $faker->realText(rand(50,100)));

try {
    $userRepository = new InMemoryUserRepository();
    $userRepository->save($user);

    echo $userRepository->get(1);

} catch (Exception $e) {
    echo 'Что-то пошло не так' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}

