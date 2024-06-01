<?php

namespace App\Repository\PostRepository;

use App\Model\Blog\Post;
use App\Model\UUID;

interface PostRepositoryInterface
{

    public function save(Post $post): void;

    public function get(UUID $uuid): Post;

}