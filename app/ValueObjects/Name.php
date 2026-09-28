<?php

declare(strict_types=1);

namespace App\ValueObjects;

readonly class Name
{
    public function __construct(public string $value)
    {
        if (strlen($this->value) > 10) {
            throw new \InvalidArgumentException('Name must be 10 or less than characters');
        }
    }
}
