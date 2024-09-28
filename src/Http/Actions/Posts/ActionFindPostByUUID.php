<?php

namespace App\Http\Actions\Posts;

use App\Exception\Http\HttpException;
use App\Exception\InvalidArgumentException;
use App\Exception\PostException\PostNotFoundException;
use App\Http\Actions\ActionInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Model\UUID;
use App\Repository\PostRepository\PostRepositoryInterface;

class ActionFindPostByUUID implements ActionInterface
{

    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Получаем UUID статьи
        try {
            $postUUID = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Ищем статью
        try {
            $post = $this->postRepository->get($postUUID);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
           'author' => $post->getUser()->getLogin(),
           'title' => $post->getTitle(),
           'text' => $post->getText()
        ]);
    }
}