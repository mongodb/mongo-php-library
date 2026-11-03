<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Update;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Performs bitwise updates of integer values.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/bit/
 * @internal
 */
final class BitOperator implements UpdateInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$bit';
    public const PROPERTIES = ['field' => 'field'];

    /**
     * @var stdClass<Document|Serializable|array|stdClass> $field Each field maps to an object containing exactly one bitwise operation:
     * and, or, or xor.
     */
    public readonly stdClass $field;

    /**
     * @param Document|Serializable|array|stdClass ...$field Each field maps to an object containing exactly one bitwise operation:
     * and, or, or xor.
     */
    public function __construct(Document|Serializable|stdClass|array ...$field)
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
