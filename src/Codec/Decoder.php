<?php

namespace MongoDB\Codec;

/**
 * @api
 *
 * @psalm-template B
 * @psalm-template T
 */
interface Decoder
{
    /**
     * @param mixed $value
     * @psalm-assert-if-true B $value
     */
    public function canDecode($value): bool;

    /**
     * @param mixed $value
     * @psalm-param B $value
     * @return mixed
     * @psalm-return T
     */
    public function decode($value);
}
