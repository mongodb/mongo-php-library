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
 * The exists operator tests if a path to a specified indexed field name exists in a document.
 *
 * New in MongoDB 5.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/exists/
 * @internal
 */
final class ExistsOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'exists';
    public const PROPERTIES = ['path' => 'path', 'score' => 'score'];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->score = $score;
    }
}
