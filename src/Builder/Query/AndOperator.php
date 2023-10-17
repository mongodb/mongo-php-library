<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;

/**
 * Joins query clauses with a logical AND returns all documents that match the conditions of both clauses.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/
 */
class AndOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<Document|QueryInterface|Serializable|array|stdClass> ...$expression */
    public readonly array $expression;

    /**
     * @param Document|QueryInterface|Serializable|array|stdClass ...$expression
     * @no-named-arguments
     */
    public function __construct(Document|Serializable|QueryInterface|stdClass|array ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$and';
    }
}
