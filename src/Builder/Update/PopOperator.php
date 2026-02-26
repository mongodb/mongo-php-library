<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Update;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Removes the first or last element of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/pop/
 * @internal
 */
final class PopOperator implements UpdateInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$pop';
    public const PROPERTIES = ['field' => 'field'];

    /** @var stdClass<int> $field */
    public readonly stdClass $field;

    /**
     * @param int ...$field
     */
    public function __construct(int ...$field)
    {
        if (\count($field) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }

        foreach($field as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $field arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $field = (object) $field;
        $this->field = $field;
    }
}
