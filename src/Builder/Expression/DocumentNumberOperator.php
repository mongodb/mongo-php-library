<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the position of a document (known as the document number) in the $setWindowFields stage partition. Ties result in different adjacent document numbers.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documentNumber/
 */
class DocumentNumberOperator implements WindowInterface
{
    public const NAME = '$documentNumber';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
