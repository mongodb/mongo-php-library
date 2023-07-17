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

namespace MongoDB\Tests\Fixtures\Codec;

use MongoDB\BSON\Document;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Tests\Fixtures\Document\TestNestedObject;
use MongoDB\Tests\Fixtures\Document\TestObject;

final class TestDocumentCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function canDecode($value): bool
    {
        return $value instanceof Document;
    }

    public function decode($value): TestObject
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        $object = new TestObject();
        $object->id = $value->get('_id');
        $object->decoded = true;

        $object->x = new TestNestedObject();
        $object->x->foo = $value->get('x')->get('foo');

        return $object;
    }

    public function canEncode($value): bool
    {
        return $value instanceof TestObject;
    }

    public function encode($value): Document
    {
        if (! $value instanceof TestObject) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP([
            '_id' => $value->id,
            'x' => Document::fromPHP(['foo' => $value->x->foo]),
            'encoded' => true,
        ]);
    }
}
