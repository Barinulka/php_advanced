<?php

namespace App\Http\Actions\Comments;

use App\Exception\Http\HttpException;
use App\Exception\InvalidArgumentException;
use App\Exception\PostException\PostNotFoundException;
use App\Exception\UserException\UserNotFoundException;
use App\Http\Actions\ActionInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Model\Blog\Comment;
use App\Model\UUID;
use App\Repository\CommentRepository\CommentRepositoryInterface;
use App\Repository\PostRepository\PostRepositoryInterface;
use App\Repository\UserRepository\UserRepositoryInterface;

class ActionCreateComment implements ActionInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Получаем UUID автора комментария
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

        // Пытемся получить UUID статьи
        try {
            $postUUID = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Пытаемся получить саму статью
        try {
            $post = $this->postRepository->get($postUUID);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        // Создаем комментарий
        try {
            $newCommentUUID = UUID::random();

            $comment = new Comment(
                $newCommentUUID,
                $author,
                $post,
                $request->jsonBodyField('text')
            );

        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->commentRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string) $newCommentUUID
        ]);
    }
}