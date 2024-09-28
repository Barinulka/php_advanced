<?php

namespace App\Http\Actions\Posts;

use App\Exception\Http\HttpException;
use App\Exception\InvalidArgumentException;
use App\Exception\UserException\UserNotFoundException;
use App\Http\Actions\ActionInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Model\Blog\Post;
use App\Model\UUID;
use App\Repository\PostRepository\PostRepositoryInterface;
use App\Repository\UserRepository\UserRepositoryInterface;

class ActionCreatePost implements ActionInterface
{

    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Пытаемся создать UUID автора
        try {
            $authorUUID = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Пытаемся получить самого автора
        try {
            $author = $this->userRepository->get($authorUUID);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Создание записи
        try {
            $newPostUUID = UUID::random();

            $post = new Post(
                $newPostUUID,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text')
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postRepository->save($post);

        return new SuccessfulResponse([
           'uuid' => (string) $newPostUUID
        ]);
    }
}