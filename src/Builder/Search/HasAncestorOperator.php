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
 * The `hasAncestor` operator queries an `embeddedDocuments` type field specified in the `ancestorPath`. The `ancestorPath` is a parent of the field specified in the `returnScope`.
 *
 * New in MongoDB 8.2
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/operators-collectors/hasancestor/
 * @internal
 */
final class HasAncestorOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'hasAncestor';
    public const PROPERTIES = ['ancestorPath' => 'ancestorPath', 'operator' => 'operator'];

    /** @var array|string $ancestorPath */
    public readonly array|string $ancestorPath;

    /** @var Document|SearchOperatorInterface|Serializable|array|stdClass $operator */
    public readonly Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /**
     * @param array|string $ancestorPath
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator
     */
    public function __construct(
        array|string $ancestorPath,
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
    ) {
        $this->ancestorPath = $ancestorPath;
        $this->operator = $operator;
    }
}
