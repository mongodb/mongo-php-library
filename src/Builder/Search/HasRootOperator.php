<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * The `hasRoot` operator can be used to query root-level fields when you specify the `returnScope` and `returnStoredSource` options.
 *
 * New in MongoDB 8.2
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/operators-collectors/hasroot/
 * @internal
 */
final class HasRootOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'hasRoot';
    public const PROPERTIES = ['operator' => 'operator'];

    /** @var Document|SearchOperatorInterface|Serializable|array|stdClass $operator */
    public readonly Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /** @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator */
    public function __construct(Document|Serializable|SearchOperatorInterface|stdClass|array $operator)
    {
        $this->operator = $operator;
    }
}
