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

namespace MongoDB\Benchmark;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\ParamProviders;

use function array_fill;
use function array_intersect_key;
use function assert;
use function file_get_contents;
use function is_array;
use function is_object;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

#[BeforeClassMethods('prepareDatabase')]
final class ReadLargeDocumentBench extends BaseBench
{
    public static function prepareDatabase(): void
    {
        $collection = self::getCollection();
        $collection->drop();

        $document = json_decode(file_get_contents(self::LARGE_FILE_PATH), false, 512, JSON_THROW_ON_ERROR);

        $documents = array_fill(0, 10, $document);

        $collection->insertMany($documents);
    }

    public function provideParams(): Generator
    {
        yield 'Driver default typemap' => [
            'codec' => null,
            'typeMap' => [],
            'accessor' => 'object',
        ];

        yield 'Array typemap' => [
            'codec' => null,
            'typeMap' => ['root' => 'array', 'array' => 'array', 'document' => 'array'],
            'accessor' => 'array',
        ];

        yield 'Library default typemap' => [
            'codec' => null,
            'typeMap' => [
                'array' => BSONArray::class,
                'document' => BSONDocument::class,
                'root' => BSONDocument::class,
            ],
            'accessor' => 'object',
        ];

        yield 'Raw BSON' => [
            'codec' => null,
            'typeMap' => ['root' => 'bson'],
            'accessor' => 'bson',
        ];
    }

    #[ParamProviders('provideParams')]
    public function benchCursorToArray(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = self::getCollection();

        $collection->find([], $options)->toArray();
    }

    #[ParamProviders('provideParams')]
    public function benchAccessId(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = self::getCollection();

        // Exhaust cursor and access identifier on each document
        foreach ($collection->find([], $options) as $document) {
            $this->accessId($document, $params['accessor']);
        }
    }

    #[ParamProviders('provideParams')]
    public function benchAccessFirstField(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = self::getCollection();

        // Exhaust cursor and access identifier on each document
        foreach ($collection->find([], $options) as $document) {
            $this->accessFirstField($document, $params['accessor']);
        }
    }

    #[ParamProviders('provideParams')]
    public function benchAccessLastField(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = self::getCollection();

        // Exhaust cursor and access identifier on each document
        foreach ($collection->find([], $options) as $document) {
            $this->accessLastField($document, $params['accessor']);
        }
    }

    /** @param array|object $document */
    private function accessId($document, string $accessor): void
    {
        switch ($accessor) {
            case 'array':
                assert(is_array($document));
                $document['_id'];
                break;

            case 'bson':
                assert($document instanceof Document);
                $document->get('_id');
                break;

            case 'object':
                assert(is_object($document));
                $document->_id;
                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid accessor "%s"', $accessor));
        }
    }

    /** @param array|object $document */
    private function accessLastField($document, string $accessor): void
    {
        switch ($accessor) {
            case 'array':
                assert(is_array($document));
                $document['Zz2MOlCxDhLl'];
                break;

            case 'bson':
                assert($document instanceof Document);
                $document->get('Zz2MOlCxDhLl');
                break;

            case 'object':
                assert(is_object($document));
                // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
                $document->Zz2MOlCxDhLl;
                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid accessor "%s"', $accessor));
        }
    }

    /** @param array|object $document */
    private function accessFirstField($document, string $accessor): void
    {
        switch ($accessor) {
            case 'array':
                assert(is_array($document));
                $document['qx3MigjubFSm'];
                break;

            case 'bson':
                assert($document instanceof Document);
                $document->get('qx3MigjubFSm');
                break;

            case 'object':
                assert(is_object($document));
                $document->qx3MigjubFSm;
                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid accessor "%s"', $accessor));
        }
    }
}
