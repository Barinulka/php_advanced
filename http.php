<?php
/*
 * Точка входа в приложение
 */

use App\Http\Actions\Posts\ActionCreatePost;
use App\Http\Actions\Posts\ActionFindPostByUUID;
use App\Http\Actions\Users\ActionCreateUser;
use App\Http\Actions\Users\ActionFindByUsername;
use App\Http\Request;
use App\Http\ErrorResponse;
use App\Repository\PostRepository\SqlitePostRepository;
use App\Repository\UserRepository\SqliteUserRepository;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => new ActionFindByUsername(
            // Действию нужен репозиторий
            new SqliteUserRepository(
            // Репозиторию нужно подключение к БД
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/show' => new ActionFindPostByUUID(
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
        ),
    ],
    'POST' => [
        '/users/create' => new ActionCreateUser(
            new SqliteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/create' => new ActionCreatePost(
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'DELETE' => [

    ],
    'PUT' => [

    ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Method Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Path Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
// Отправляем ответ
$response->send();