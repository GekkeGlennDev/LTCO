<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\UserInfo;
use App\Models\User;
use App\Services\PasswordGenerator;
use App\Services\UserService;
use App\ValueObjects\Email;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

#[Signature('app:create-user')]
#[Description('Create a user, e.g. the first admin')]
class CreateUser extends Command
{
    public function handle(UserService $userService): int
    {
        $name = $this->ask('Name of the user');
        $email = new Email($this->ask('Email of the user'));
        $isAdmin = $this->confirm('Is the user an admin?');

        $userInfo = new UserInfo($name, $email, $isAdmin);
        $validator = Validator::make($userInfo->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = $userService->storeUser($userInfo);

        $this->components->info("User {$user->name} created" . ($user->is_admin ? ' as admin.' : '.'));
        $this->components->info('Password: ' . $userInfo->password->value);

        return self::SUCCESS;
    }
}
