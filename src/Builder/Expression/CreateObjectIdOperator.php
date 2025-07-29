<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Returns a random object ID
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/createObjectId/
 * @internal
 */
final class CreateObjectIdOperator implements ResolvesToObjectId, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$createObjectId';

    public function __construct()
    {
    }
}
