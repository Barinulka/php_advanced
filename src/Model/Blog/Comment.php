<?php

namespace App\Model\Blog;

class Comment {
    public function __construct(
        private int $id,
        private User $user,
        private Post $post,
        private string $text
    ){
    }

    public function __toString() {
        return $this->user . " пишет Коммент " . $this->text;
    }
}