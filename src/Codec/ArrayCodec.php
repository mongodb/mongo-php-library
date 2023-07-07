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

use MongoDB\Exception\UnsupportedValueException;

use function array_map;
use function is_array;

/**
 * Codec to recursively encode/decode values in arrays.
 *
 * @template-implements Codec<array, array>
 */
final class ArrayCodec implements Codec, KnowsCodecLibrary
{
    private ?CodecLibrary $library = null;

    public function attachCodecLibrary(CodecLibrary $library): void
    {
        $this->library = $library;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true array $value
     */
    public function canDecode($value): bool
    {
        return is_array($value);
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true array $value
     */
    public function canEncode($value): bool
    {
        return is_array($value);
    }

    /** @param mixed $value */
    public function decode($value): array
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return array_map(
            /**
             * @param mixed $item
             * @return mixed
             */
            fn ($item) => $this->getLibrary()->decodeIfSupported($item),
            $value,
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is array ? array : $value)
     */
    public function decodeIfSupported($value)
    {
        return $this->canDecode($value) ? $this->decode($value) : $value;
    }

    /** @param mixed $value */
    public function encode($value): array
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return array_map(
            /**
             * @param mixed $item
             * @return mixed
             */
            fn ($item) => $this->getLibrary()->encodeIfSupported($item),
            $value,
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is array ? array : $value)
     */
    public function encodeIfSupported($value)
    {
        return $this->canEncode($value) ? $this->encode($value) : $value;
    }

    private function getLibrary(): CodecLibrary
    {
        if (! $this->library) {
            $this->library = new CodecLibrary();
        }

        return $this->library;
    }
}
