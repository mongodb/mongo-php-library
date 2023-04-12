<?php

namespace MongoDB\Codec;

/**
 * @api
 *
 * @psalm-template B
 * @psalm-template T
 * @template-extends Decoder<B, T>
 * @template-extends Encoder<B, T>
 */
interface Codec extends Decoder, Encoder
{
}
