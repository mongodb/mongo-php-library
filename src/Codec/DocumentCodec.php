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

/**
 * The DocumentCodec interface allows decoding BSON document data to native PHP
 * objects and back to BSON documents.
 *
 * @psalm-template ObjectType of object
 * @template-extends Codec<Document, ObjectType>
 */
interface DocumentCodec extends Codec
{
    /**
     * @psalm-param Document $value
     * @psalm-return ObjectType
     * @throws UnsupportedValueException if the decoder does not support the value
     */
    public function decode(mixed $value): object;

    /**
     * @psalm-param ObjectType $value
     * @throws UnsupportedValueException if the encoder does not support the value
     */
    public function encode(mixed $value): Document;
}
