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
use MongoDB\BSON\PackedArray;
use PhpBench\Attributes\BeforeMethods;

use function array_values;
use function file_get_contents;
use function iterator_to_array;
use function json_decode;
use function MongoDB\BSON\fromPHP;

use const JSON_THROW_ON_ERROR;

#[BeforeMethods('prepareData')]
final class PackedArrayBench extends BaseBench
{
    private static string $rawBSONDocument;
    private static PackedArray $array;

    public function prepareData(): void
    {
        $array = array_values(json_decode(file_get_contents(self::LARGE_FILE_PATH), true, 512, JSON_THROW_ON_ERROR));

        // Store BSON string for a document since we can't create packed arrays from BSON strings
        self::$rawBSONDocument = fromPHP(['array' => $array]);
    }

    /** Create a document from the BSON string */
    public static function getBSONArray(): PackedArray
    {
        return self::$array ??= Document::fromBSON(self::$rawBSONDocument)->get('array');
    }

    public function benchCheckFirst(): void
    {
        self::getBSONArray()->has(0);
    }

    public function benchCheckLast(): void
    {
        self::getBSONArray()->has(94354);
    }

    public function benchAccessFirst(): void
    {
        self::getBSONArray()->get(0);
    }

    public function benchAccessLast(): void
    {
        self::getBSONArray()->get(94354);
    }

    public function benchIteratorToArray(): void
    {
        iterator_to_array(self::getBSONArray());
    }

    public function benchToPHPArray(): void
    {
        self::getBSONArray()->toPHP();
    }

    public function benchIteration(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::getBSONArray() as $key => $value) {
        }
    }

    public function benchIterationAfterIteratorToArray(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (iterator_to_array(self::getBSONArray()) as $key => $value) {
        }
    }

    public function benchIterationAsArray(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::getBSONArray()->toPHP() as $key => $value) {
        }
    }
}
