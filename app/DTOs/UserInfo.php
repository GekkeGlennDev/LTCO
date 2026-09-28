<?php

declare(strict_types=1);

namespace App\DTOs;

use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Contracts\Support\Arrayable;

class UserInfo implements Arrayable
{
    public function __construct(
        public readonly string $name,
        public readonly Email $email,
        public readonly bool $isAdmin,
        #[\SensitiveParameter] public ?Password $password = null,
    ) {
    }

    public function toArray(): array
    {
        if ($this->password === null) {
            return [
                'name' => $this->name,
                'email' => $this->email->value,
                'is_admin' => $this->isAdmin,
            ];
        }

        return [
            'name' => $this->name,
            'email' => $this->email->value,
            'password' => bcrypt($this->password->value),
            'is_admin' => $this->isAdmin,
        ];
    }
}
