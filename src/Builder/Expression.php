<?php

declare(strict_types=1);

namespace MongoDB\Builder;

/**
 * Factories for Aggregation Pipeline Expression Operators
 *
 * @see https://docs.mongodb.com/manual/reference/operator/aggregation/
 */
enum Expression
{
    use Expression\ExpressionFactoryTrait;
    use Expression\FactoryTrait;
}
