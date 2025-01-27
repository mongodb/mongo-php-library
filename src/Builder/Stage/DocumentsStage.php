<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns literal documents from input values.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documents/
 * @internal
 */
final class DocumentsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$documents';
    public const PROPERTIES = ['documents' => 'documents'];

    /**
     * @var BSONArray|PackedArray|ResolvesToArray|array|string $documents $documents accepts any valid expression that resolves to an array of objects. This includes:
     * - system variables, such as $$NOW or $$SEARCH_META
     * - $let expressions
     * - variables in scope from $lookup expressions
     * Expressions that do not resolve to a current document, like $myField or $$ROOT, will result in an error.
     */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $documents;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $documents $documents accepts any valid expression that resolves to an array of objects. This includes:
     * - system variables, such as $$NOW or $$SEARCH_META
     * - $let expressions
     * - variables in scope from $lookup expressions
     * Expressions that do not resolve to a current document, like $myField or $$ROOT, will result in an error.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array|string $documents)
    {
        if (is_string($documents) && ! str_starts_with($documents, '$')) {
            throw new InvalidArgumentException('Argument $documents can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($documents) && ! array_is_list($documents)) {
            throw new InvalidArgumentException('Expected $documents argument to be a list, got an associative array.');
        }

        $this->documents = $documents;
    }
}
