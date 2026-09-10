<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Update;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function array_first;
use function array_key_first;
use function assert;
use function count;
use function get_object_vars;
use function is_string;

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

    /** @return array<string, mixed> */
    public function encode(mixed $value): array
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $encoded = [];
        foreach ($value->update as $operator) {
            $array = (array) $this->recursiveEncode($operator);
            assert(count($array) === 1);

            $key = array_key_first($array);
            $operator = array_first($array);

            assert(is_string($key));
            assert($operator instanceof stdClass);

            if (isset($encoded[$key])) {
                foreach (get_object_vars($operator) as $field => $value) {
                    $encoded[$key]->{$field} = $value;
                }
            } else {
                $encoded[$key] = $operator;
            }
        }

        return $encoded;
    }
}
