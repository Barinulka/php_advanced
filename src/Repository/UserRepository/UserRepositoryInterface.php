<?php

namespace App\Repository\UserRepository;

use App\Model\Blog\User;
use App\Model\UUID;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function get(UUID $uuid): User;

    public function getByUsername(string $username): User;
}