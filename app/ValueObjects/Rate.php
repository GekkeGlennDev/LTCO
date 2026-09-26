<?php

declare(strict_types=1);

namespace App\ValueObjects;

readonly class Rate
{
    public float $value;

    public function __construct(string $rate)
    {
        if (!is_numeric($rate))
        {
            throw new \InvalidArgumentException('Rate must be numeric');
        }

        $this->value = floatval($rate);
    }
}
