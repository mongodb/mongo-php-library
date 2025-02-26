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
 * Reshapes each document in the stream, such as by adding new fields or removing existing fields. For each input document, outputs one document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/
 * @internal
 */
final class ProjectStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$project';
    public const PROPERTIES = ['specification' => 'specification'];

    /** @var stdClass<DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string> $specification */
    public readonly stdClass $specification;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$specification
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$specification,
    ) {
        if (\count($specification) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $specification, got %d.', 1, \count($specification)));
        }

        foreach($specification as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $specification arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $specification = (object) $specification;
        $this->specification = $specification;
    }
}
