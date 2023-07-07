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

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Model\LazyBSONArray;

/**
 * Codec for lazy decoding of BSON PackedArray instances
 *
 * @template-implements Codec<PackedArray, LazyBSONArray>
 */
final class LazyBSONArrayCodec implements Codec, KnowsCodecLibrary
{
    private ?CodecLibrary $library = null;

    public function attachCodecLibrary(CodecLibrary $library): void
    {
        $this->library = $library;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true PackedArray $value
     */
    public function canDecode($value): bool
    {
        return $value instanceof PackedArray;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true LazyBSONArray $value
     */
    public function canEncode($value): bool
    {
        return $value instanceof LazyBSONArray;
    }

    /** @param mixed $value */
    public function decode($value): LazyBSONArray
    {
        if (! $value instanceof PackedArray) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return new LazyBSONArray($value, $this->getLibrary());
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is PackedArray ? LazyBSONArray : $value)
     */
    public function decodeIfSupported($value)
    {
        return $this->canDecode($value) ? $this->decode($value) : $value;
    }

    /** @param mixed $value */
    public function encode($value): PackedArray
    {
        if (! $value instanceof LazyBSONArray) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $return = [];
        /** @var mixed $offsetValue */
        foreach ($value as $offsetValue) {
            $return[] = $this->getLibrary()->encodeIfSupported($offsetValue);
        }

        return PackedArray::fromPHP($return);
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is LazyBSONArray ? PackedArray : $value)
     */
    public function encodeIfSupported($value)
    {
        return $this->canEncode($value) ? $this->encode($value) : $value;
    }

    private function getLibrary(): CodecLibrary
    {
        if (! $this->library) {
            $this->library = new LazyBSONCodecLibrary();
        }

        return $this->library;
    }
}
