<?php

use App\Model\Blog\Post;
use App\Model\Blog\User;
use App\Model\Person\Name;
use App\Model\Person\Person;
use App\Repository\UserRepository\InMemoryUserRepository;

//spl_autoload_register('load');
include 'functions.php';
include __DIR__ . '/vendor/autoload.php';
function load($className) {
    $file = $className . '.php';

    $file = str_replace("/", DIRECTORY_SEPARATOR, $file);

    if (file_exists($file)) {
        include $file;
    }
}

$name = new Name("Ivan", "Ivanov");
$user = new User(1, $name, 'Admin');
$person = new Person($name, new DateTimeImmutable());
$post = new Post(1, $person, 'some text');

try {
    $userRepository = new InMemoryUserRepository();
    $userRepository->save($user);

    echo $userRepository->get(444);

} catch (Exception $e) {
    echo 'Что-то пошло не так' . PHP_EOL;
    echo $e->getMessage();
}

