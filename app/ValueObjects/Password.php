<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;

readonly class Password
{
    public function __construct(public string $value)
    {
        $validator = Validator::make(
            ['password' => $this->value],
            ['password' => PasswordRule::defaults()],
        );

        if ($validator->fails()) {
            $exceptionMessage = sprintf(
                'The password does not met security requirements%s%s',
                PHP_EOL,
                implode(PHP_EOL, $validator->messages()->all())
            );

            throw new \InvalidArgumentException($exceptionMessage);
        }
    }
}
