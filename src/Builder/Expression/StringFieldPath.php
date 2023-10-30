<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Exception\InvalidArgumentException;

use function sprintf;
use function str_starts_with;

class StringFieldPath implements FieldPathInterface, ResolvesToString
{
    public readonly string $name;

    public function __construct(string $name)
    {
        if (str_starts_with($name, '$')) {
            throw new InvalidArgumentException(sprintf('Name cannot start with a dollar sign: "%s"', $name));
        }

        $this->name = $name;
    }
}
