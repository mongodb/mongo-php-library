<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\BuilderEncoder;
use stdClass;

use function get_object_vars;
use function is_array;

trait RecursiveEncode
{
    final public function __construct(protected readonly BuilderEncoder $encoder)
    {
    }

    /**
     * Nested arrays and objects must be encoded recursively.
     *
     * @psalm-template T
     * @psalm-param T $value
     *
     * @psalm-return (T is stdClass ? stdClass : (T is array ? array : mixed))
     *
     * @template T
     */
    private function recursiveEncode(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->recursiveEncode($val);
            }

            return $value;
        }

        if ($value instanceof stdClass) {
            foreach (get_object_vars($value) as $key => $val) {
                $value->{$key} = $this->recursiveEncode($val);
            }

            return $value;
        }

        return $this->encoder->encodeIfSupported($value);
    }
}
