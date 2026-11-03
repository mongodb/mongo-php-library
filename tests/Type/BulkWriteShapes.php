<?php
/*
 * Copyright 2015-present MongoDB, Inc.
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

namespace MongoDB\Tests\Type;

use MongoDB\Collection;

/**
 * Psalm type tests for bulk write operation shapes.
 *
 * This file is not executed by PHPUnit; it is only checked by Psalm to verify
 * that the OperationShape defined in BulkWrite is accepted by Collection::bulkWrite.
 */
final class BulkWriteShapes
{
    /** @see https://www.mongodb.com/docs/php-library/current/write/bulk-write/ */
    public function bulkWrite(Collection $collection): void
    {
        $collection->bulkWrite([
            ['insertOne' => [['x' => 1]]],
            ['updateOne' => [['x' => 1], ['$set' => ['y' => 1]]]],
            ['updateMany' => [['x' => ['$gt' => 1]], ['$inc' => ['x' => 1]]]],
            ['replaceOne' => [['x' => 1], ['y' => 1]]],
            ['deleteOne' => [['x' => 1]]],
            ['deleteMany' => [['x' => ['$gt' => 5]]]],
        ]);
    }
}
