<?php

namespace App\Model\Blog;

use App\Model\Person\Person;

class Post
{
    public function __construct(
        private int $id,
        private User $author,
        private string $text
    ){
    }

    public function __toString(): string
    {
        return $this->author . ' пишет: ' . $this->text . PHP_EOL;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Person
     */
    public function getAuthor(): Person
    {
        return $this->author;
    }

    /**
     * @param Person $author
     */
    public function setAuthor(Person $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }


}