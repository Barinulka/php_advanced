<?php

namespace App\Repository\CommentRepository;

use App\Model\Blog\Comment;
use App\Model\UUID;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;
}