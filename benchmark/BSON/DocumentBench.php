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

namespace MongoDB\Benchmark\BSON;

use MongoDB\Benchmark\BaseBench;
use MongoDB\BSON\Document;
use PhpBench\Attributes\BeforeMethods;

use function file_get_contents;
use function iterator_to_array;
use function MongoDB\BSON\fromJSON;

#[BeforeMethods('prepareData')]
final class DocumentBench extends BaseBench
{
    private static string $rawBSON;
    private static Document $document;

    public function prepareData(): void
    {
        self::$rawBSON = fromJSON(file_get_contents(self::LARGE_FILE_PATH));
    }

    /** Create a document from the BSON string */
    public static function getBSONDocument(): Document
    {
        return self::$document ??= Document::fromBSON(self::$rawBSON);
    }

    public function benchCheckFirst(): void
    {
        self::getBSONDocument()->has('qx3MigjubFSm');
    }

    public function benchCheckLast(): void
    {
        self::getBSONDocument()->has('Zz2MOlCxDhLl');
    }

    public function benchAccessFirst(): void
    {
        self::getBSONDocument()->get('qx3MigjubFSm');
    }

    public function benchAccessLast(): void
    {
        self::getBSONDocument()->get('Zz2MOlCxDhLl');
    }

    public function benchIteratorToArray(): void
    {
        iterator_to_array(self::getBSONDocument());
    }

    public function benchToPHPObject(): void
    {
        self::getBSONDocument()->toPHP();
    }

    public function benchToPHPArray(): void
    {
        self::getBSONDocument()->toPHP(['root' => 'array']);
    }

    public function benchIteration(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::getBSONDocument() as $key => $value);
    }

    public function benchIterationAsArray(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::getBSONDocument()->toPHP(['root' => 'array']) as $key => $value);
    }

    public function benchIterationAsObject(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::getBSONDocument()->toPHP() as $key => $value);
    }
}
