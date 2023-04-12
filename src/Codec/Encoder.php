<?php

namespace MongoDB\Codec;

/**
 * @api
 *
 * @psalm-template B
 * @psalm-template T
 */
interface Encoder
{
    /**
     * @param mixed $value
     * @psalm-assert-if-true T $value
     */
    public function canEncode($value): bool;

    /**
     * @param mixed $value
     * @psalm-param T $value
     * @return mixed
     * @psalm-return B
     */
    public function encode($value);
}
