<?php

declare(strict_types=1);

namespace App\Services;

use App\ValueObjects\Password;
use InvalidArgumentException;
use Random\Randomizer;

/**
 * Generates random passwords with a cryptographically secure random source.
 *
 * Every character set is guaranteed to be used at least once, and look-alike
 * characters (0/O, 1/l/I) are left out so passwords can be typed over safely.
 */
class PasswordGenerator
{
    public const int DEFAULT_LENGTH = 16;

    private const array CHARACTER_SETS = [
        'abcdefghijkmnopqrstuvwxyz',
        'ABCDEFGHJKLMNPQRSTUVWXYZ',
        '23456789',
        '!#$%&*+-=?@_',
    ];

    public function __construct(private readonly Randomizer $randomizer = new Randomizer)
    {
    }

    public function generate(int $length = self::DEFAULT_LENGTH): Password
    {
        if ($length < 8) {
            throw new InvalidArgumentException(
                'The password length must be at least 8.'
            );
        }

        $allCharacters = implode('', self::CHARACTER_SETS);

        // One character from every set, then fill up from all sets combined.
        $characters = array_map($this->pickFrom(...), self::CHARACTER_SETS);

        while (count($characters) < $length) {
            $characters[] = $this->pickFrom($allCharacters);
        }

        // Shuffle so the guaranteed characters don't always come first.
        return new Password(implode('', $this->randomizer->shuffleArray($characters)));
    }

    private function pickFrom(string $characters): string
    {
        // getInt() is uniform over the range, so there is no modulo bias.
        return $characters[$this->randomizer->getInt(0, strlen($characters) - 1)];
    }
}
