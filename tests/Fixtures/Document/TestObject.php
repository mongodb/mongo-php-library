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

namespace MongoDB\Tests\Fixtures\Document;

use MongoDB\BSON\Document;

final class TestObject
{
    public int $id;

    public TestNestedObject $x;

    public bool $decoded = false;

    public static function createDocument(int $id): Document
    {
        return Document::fromPHP([
            '_id' => $id,
            'x' => ['foo' => 'bar'],
        ]);
    }

    public static function createForFixture(int $id): self
    {
        $instance = new self();
        $instance->id = $id;

        $instance->x = new TestNestedObject();
        $instance->x->foo = 'bar';

        return $instance;
    }

    public static function createDecodedForFixture(int $id): self
    {
        $instance = self::createForFixture($id);
        $instance->decoded = true;

        return $instance;
    }
}
