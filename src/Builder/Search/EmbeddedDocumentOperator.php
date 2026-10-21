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
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * The embeddedDocument operator is similar to $elemMatch operator.
 * It constrains multiple query predicates to be satisfied from a single
 * element of an array of embedded documents. embeddedDocument can be used only
 * for queries over fields of the embeddedDocuments
 *
 * New in MongoDB 5.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/embedded-document/
 * @internal
 */
final class EmbeddedDocumentOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'embeddedDocument';
    public const PROPERTIES = ['path' => 'path', 'operator' => 'operator', 'score' => 'score'];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var Document|SearchOperatorInterface|Serializable|array|stdClass $operator */
    public readonly Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->operator = $operator;
        $this->score = $score;
    }
}
