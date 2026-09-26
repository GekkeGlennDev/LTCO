<?php

declare(strict_types=1);

namespace App\ValueObjects;

readonly class Name
{
    public string $value;

    public function __construct(string $name)
    {
        if (strlen($name) > 10) {
            throw new \InvalidArgumentException('Name must be 10 or less than characters');
        }

        $this->value = $name;
    }
}
