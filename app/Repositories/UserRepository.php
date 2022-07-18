<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * @codeCoverageIgnore
     */
    public function save(User $user): void
    {
        $user->saveOrFail();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getByEmail(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}
