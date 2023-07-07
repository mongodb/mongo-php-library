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
use stdClass;

use function get_object_vars;

/**
 * Codec for lazy decoding of BSON PackedArray instances
 *
 * @template-implements Codec<stdClass, stdClass>
 */
final class ObjectCodec implements Codec, KnowsCodecLibrary
{
    private ?CodecLibrary $library = null;

    public function attachCodecLibrary(CodecLibrary $library): void
    {
        $this->library = $library;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true stdClass $value
     */
    public function canDecode($value): bool
    {
        return $value instanceof stdClass;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true stdClass $value
     */
    public function canEncode($value): bool
    {
        return $value instanceof stdClass;
    }

    /** @param mixed $value */
    public function decode($value): stdClass
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        $return = new stdClass();

        /** @var mixed $item */
        foreach (get_object_vars($value) as $key => $item) {
            $return->{$key} = $this->getLibrary()->decodeIfSupported($item);
        }

        return $return;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is stdClass ? stdClass : $value)
     */
    public function decodeIfSupported($value)
    {
        return $this->canDecode($value) ? $this->decode($value) : $value;
    }

    /** @param mixed $value */
    public function encode($value): stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $return = new stdClass();

        /** @var mixed $item */
        foreach (get_object_vars($value) as $key => $item) {
            $return->{$key} = $this->getLibrary()->encodeIfSupported($item);
        }

        return $return;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is stdClass ? stdClass : $value)
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
