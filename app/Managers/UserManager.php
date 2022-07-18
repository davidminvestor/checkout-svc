<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\User;
use App\Repositories\UserRepository;

class UserManager
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function createToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function register(User $user): string
    {
        $this->userRepository->save($user);
        return $this->createToken($user);
    }

    public function login(string $email): string
    {
        $user = $this->userRepository->getByEmail($email);
        return $this->createToken($user);
    }
}
