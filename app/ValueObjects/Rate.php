<?php

declare(strict_types=1);

namespace App\ValueObjects;

readonly class Rate
{
    public float $value;

    public function __construct(string $value)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Rate must be numeric');
        }

        $this->value = floatval($value);
    }
}
