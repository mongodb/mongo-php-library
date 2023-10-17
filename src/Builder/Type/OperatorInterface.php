<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

/**
 * Marker interface for MongoDB operators.
 */
interface OperatorInterface
{
    public function getOperator(): string;
}
