<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Sort;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Reorders the document stream by a specified sort key. Only the order changes; the documents remain unmodified. For each input document, outputs one document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/
 * @internal
 */
final class SortStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$sort';
    public const PROPERTIES = ['sort' => 'sort'];

    /** @var stdClass<DateTimeInterface|ExpressionInterface|Sort|Type|array|bool|float|int|null|stdClass|string> $sort */
    public readonly stdClass $sort;

    /**
     * @param DateTimeInterface|ExpressionInterface|Sort|Type|array|bool|float|int|null|stdClass|string ...$sort
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|Sort|stdClass|array|bool|float|int|null|string ...$sort,
    ) {
        if (\count($sort) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $sort, got %d.', 1, \count($sort)));
        }

        foreach($sort as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $sort arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $sort = (object) $sort;
        $this->sort = $sort;
    }
}
