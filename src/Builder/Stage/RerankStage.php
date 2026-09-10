<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Reranks documents using a Voyage AI reranking model to improve relevance scoring.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/vector-search/query/aggregation-stages/rerank/
 * @internal
 */
final class RerankStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$rerank';

    public const PROPERTIES = [
        'model' => 'model',
        'query' => 'query',
        'path' => 'path',
        'numDocsToRerank' => 'numDocsToRerank',
    ];

    /** @var string $model Name of the Voyage AI reranking model to use (e.g. rerank-2.5, rerank-2.5-lite). */
    public readonly string $model;

    /** @var Document|Serializable|array|stdClass $query Query object for reranking. */
    public readonly Document|Serializable|stdClass|array $query;

    /** @var BSONArray|PackedArray|array|string $path Field path or array of field paths to use for reranking. */
    public readonly PackedArray|BSONArray|array|string $path;

    /** @var int<1, 1000> $numDocsToRerank Maximum number of documents to send to Voyage AI for reranking. Value must be between 1 and 1000. */
    public readonly int $numDocsToRerank;

    /**
     * @param string $model Name of the Voyage AI reranking model to use (e.g. rerank-2.5, rerank-2.5-lite).
     * @param Document|Serializable|array|stdClass $query Query object for reranking.
     * @param BSONArray|PackedArray|array|string $path Field path or array of field paths to use for reranking.
     * @param int<1, 1000> $numDocsToRerank Maximum number of documents to send to Voyage AI for reranking. Value must be between 1 and 1000.
     */
    public function __construct(
        string $model,
        Document|Serializable|stdClass|array $query,
        PackedArray|BSONArray|array|string $path,
        int $numDocsToRerank,
    ) {
        $this->model = $model;
        $this->query = $query;
        if (is_array($path) && ! array_is_list($path)) {
            throw new InvalidArgumentException('Expected $path argument to be a list, got an associative array.');
        }

        $this->path = $path;
        $this->numDocsToRerank = $numDocsToRerank;
    }
}
