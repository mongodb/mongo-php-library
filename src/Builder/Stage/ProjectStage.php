<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\ProjectionInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Reshapes each document in the stream, such as by adding new fields or removing existing fields. For each input document, outputs one document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/
 */
class ProjectStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var stdClass<Document|ProjectionInterface|ResolvesToBool|Serializable|array|bool|int|stdClass> $specification */
    public readonly stdClass $specification;

    /**
     * @param Document|ProjectionInterface|ResolvesToBool|Serializable|array|bool|int|stdClass ...$specification
     */
    public function __construct(
        Document|Serializable|ResolvesToBool|ProjectionInterface|stdClass|array|bool|int ...$specification,
    ) {
        if (\count($specification) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $specification, got %d.', 1, \count($specification)));
        }
        foreach($specification as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $specification arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        $specification = (object) $specification;
        $this->specification = $specification;
    }

    public function getOperator(): string
    {
        return '$project';
    }
}
