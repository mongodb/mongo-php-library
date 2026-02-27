<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Builder\Update;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function assert;
use function count;
use function current;
use function get_object_vars;
use function key;

/**
 * @template-implements Encoder<array<string, mixed>, Update>
 * @internal
 */
final class UpdateEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<array<string, mixed>, Update> */
    use EncodeIfSupported;
    use RecursiveEncode;

    /** @psalm-assert-if-true Update $value */
    public function canEncode(mixed $value): bool
    {
        return $value instanceof Update;
    }

    /** @return list<mixed> */
    public function encode(mixed $value): array
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        assert($value instanceof Update);

        $encoded = [];
        foreach ($value->update as $key => $operator) {
            assert($operator instanceof UpdateInterface);
            $array = (array) $this->recursiveEncode($operator);
            assert(count($array) === 1);
            $key = key($array);
            $operator = current($operator);
            assert($operator instanceof stdClass);

            if (isset($encoded[$key])) {
                foreach (get_object_vars($encoded[$key]) as $field => $value) {
                    $encoded[$key]->{$field} = $value;
                }
            } else {
                $encoded[$key] = $operator;
            }
        }

        return $encoded;
    }
}
