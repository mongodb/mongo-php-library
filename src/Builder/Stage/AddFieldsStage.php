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
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Adds new fields to documents. Outputs documents that contain all existing fields from the input documents and newly added fields.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/
 * @internal
 */
final class AddFieldsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$addFields';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var stdClass<DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string> $expression Specify the name of each field to add and set its value to an aggregation expression or an empty object. */
    public readonly stdClass $expression;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$expression Specify the name of each field to add and set its value to an aggregation expression or an empty object.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ) {
        if (\count($expression) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        foreach($expression as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $expression arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $expression = (object) $expression;
        $this->expression = $expression;
    }
}
