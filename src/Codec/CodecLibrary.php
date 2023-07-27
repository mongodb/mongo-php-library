<?php
/*
 * Copyright 2023-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Codec;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;

/** @template-implements Codec<mixed, mixed> */
class CodecLibrary implements Codec
{
    /** @template-use DecodeIfSupported<mixed, mixed> */
    use DecodeIfSupported;
    /** @template-use EncodeIfSupported<mixed, mixed> */
    use EncodeIfSupported;

    /** @var list<Decoder> */
    private array $decoders = [];

    /** @var list<Encoder> */
    private array $encoders = [];

    /** @param Decoder|Encoder $items */
    public function __construct(...$items)
    {
        foreach ($items as $item) {
            if (! $item instanceof Decoder && ! $item instanceof Encoder) {
                throw InvalidArgumentException::invalidType('$items', $item, [Decoder::class, Encoder::class]);
            }

            if ($item instanceof Codec) {
                // Use attachCodec to avoid multiple calls to attachLibrary
                $this->attachCodec($item);

                continue;
            }

            if ($item instanceof Decoder) {
                $this->attachDecoder($item);
            }

            if ($item instanceof Encoder) {
                $this->attachEncoder($item);
            }
        }
    }

    /** @return static */
    final public function attachCodec(Codec $codec): self
    {
        $this->decoders[] = $codec;
        $this->encoders[] = $codec;
        if ($codec instanceof CodecLibraryAware) {
            $codec->attachCodecLibrary($this);
        }

        return $this;
    }

    /** @return static */
    final public function attachDecoder(Decoder $decoder): self
    {
        $this->decoders[] = $decoder;
        if ($decoder instanceof CodecLibraryAware) {
            $decoder->attachCodecLibrary($this);
        }

        return $this;
    }

    /** @return static */
    final public function attachEncoder(Encoder $encoder): self
    {
        $this->encoders[] = $encoder;
        if ($encoder instanceof CodecLibraryAware) {
            $encoder->attachCodecLibrary($this);
        }

        return $this;
    }

    /** @param mixed $value */
    final public function canDecode($value): bool
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->canDecode($value)) {
                return true;
            }
        }

        return false;
    }

    /** @param mixed $value */
    final public function canEncode($value): bool
    {
        foreach ($this->encoders as $encoder) {
            if ($encoder->canEncode($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    final public function decode($value)
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->canDecode($value)) {
                return $decoder->decode($value);
            }
        }

        throw UnsupportedValueException::invalidDecodableValue($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    final public function encode($value)
    {
        foreach ($this->encoders as $encoder) {
            if ($encoder->canEncode($value)) {
                return $encoder->encode($value);
            }
        }

        throw UnsupportedValueException::invalidEncodableValue($value);
    }
}
