<?php

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
