<?php

declare(strict_types=1);

namespace MongoDB\Builder;

/**
 * Factories for Update Operators
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/
 */
final class Update
{
    use Update\FactoryTrait;

    private function __construct()
    {
        // This class cannot be instantiated
    }
}

