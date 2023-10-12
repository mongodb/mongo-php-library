<?php

namespace MongoDB\Builder;

use MongoDB\Builder\Projection\FactoryTrait;

/**
 * Factories for Projection Operators
 *
 * @see https://docs.mongodb.com/manual/reference/operator/projection/
 */
enum Projection
{
    use FactoryTrait;
}
