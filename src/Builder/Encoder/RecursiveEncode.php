<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Codec\Encoder;
use stdClass;
use WeakReference;

use function get_object_vars;
use function is_array;

/** @internal */
trait RecursiveEncode
{
    /** @param WeakReference<Encoder> $encoder */
    final public function __construct(private readonly WeakReference $encoder)
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

        /**
         * If the BuilderEncoder instance is removed from the memory, the
         * instances of the classes using this trait will be removed as well.
         * Therefore, the weak reference will never return null.
         *
         * @psalm-suppress PossiblyNullReference
         */
        return $this->encoder->get()->encodeIfSupported($value);
    }
}
