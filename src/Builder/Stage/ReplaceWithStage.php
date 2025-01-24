<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Replaces a document with the specified embedded document. The operation replaces all existing fields in the input document, including the _id field. Specify a document embedded in the input document to promote the embedded document to the top level.
 * Alias for $replaceRoot.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/
 * @internal
 */
final class ReplaceWithStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$replaceWith';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var Document|ResolvesToObject|Serializable|array|stdClass|string $expression */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array|string $expression;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $expression
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array|string $expression)
    {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression = $expression;
    }
}
