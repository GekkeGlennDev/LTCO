<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserInfo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        private readonly PasswordGenerator $passwordGenerator
    ) {
    }

    public function storeUser(UserInfo $userInfo): User
    {
        $password = $this->passwordGenerator->generate();
        $userInfo->password = $password;

        return User::create($userInfo->toArray());
    }

    public function updateUser(User $user, UserInfo $userInfo, bool $generatePassword): User
    {
        if ($generatePassword) {
            $password = $this->passwordGenerator->generate();
            $userInfo->password = $password;
        }

        $user->update($userInfo->toArray());

        return $user;
    }
}
