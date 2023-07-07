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

use MongoDB\BSON\Document;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Model\LazyBSONDocument;

/**
 * Codec for lazy decoding of BSON Document instances
 *
 * @template-implements DocumentCodec<LazyBSONDocument>
 */
final class LazyBSONDocumentCodec implements DocumentCodec, KnowsCodecLibrary
{
    private ?CodecLibrary $library = null;

    public function attachCodecLibrary(CodecLibrary $library): void
    {
        $this->library = $library;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true Document $value
     */
    public function canDecode($value): bool
    {
        return $value instanceof Document;
    }

    /**
     * @param mixed $value
     * @psalm-assert-if-true LazyBSONDocument $value
     */
    public function canEncode($value): bool
    {
        return $value instanceof LazyBSONDocument;
    }

    /** @param mixed $value */
    public function decode($value): LazyBSONDocument
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return new LazyBSONDocument($value, $this->getLibrary());
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is Document ? LazyBSONDocument : $value)
     */
    public function decodeIfSupported($value)
    {
        return $this->canDecode($value) ? $this->decode($value) : $value;
    }

    /** @param mixed $value */
    public function encode($value): Document
    {
        if (! $value instanceof LazyBSONDocument) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $return = [];
        /** @var mixed $fieldValue */
        foreach ($value as $field => $fieldValue) {
            $return[$field] = $this->getLibrary()->encodeIfSupported($fieldValue);
        }

        return Document::fromPHP($return);
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return ($value is LazyBSONDocument ? Document : $value)
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
