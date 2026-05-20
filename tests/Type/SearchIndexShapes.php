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
 * Psalm type tests for search index shapes.
 *
 * This file is not executed by PHPUnit; it is only checked by Psalm to verify
 * that the array shapes defined in Collection are accepted by its methods.
 */
final class SearchIndexShapes
{
    /** @see https://www.mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/#create-a-search-index-on-all-fields */
    public function createSearchIndexOnAllFields(Collection $collection): void
    {
        $collection->createSearchIndex(
            ['mappings' => ['dynamic' => true]],
            ['name' => 'searchIndex01'],
        );
    }

    /** @see https://www.mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/#create-a-search-index-with-a-language-analyzer */
    public function createSearchIndexWithLanguageAnalyzer(Collection $collection): void
    {
        $collection->createSearchIndex(
            [
                'mappings' => [
                    'fields' => [
                        'subject' => [
                            'fields' => [
                                'fr' => [
                                    'analyzer' => 'lucene.french',
                                    'type' => 'string',
                                ],
                            ],
                            'type' => 'document',
                        ],
                    ],
                ],
            ],
            ['name' => 'frenchIndex01'],
        );
    }

    /** @see https://www.mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/#create-a-search-index-with-the-default-name */
    public function createSearchIndexWithDefaultName(Collection $collection): void
    {
        $collection->createSearchIndex([
            'mappings' => [
                'fields' => [
                    'title' => ['type' => 'string'],
                ],
            ],
        ]);
    }

    public function createSearchIndexWithDynamicFalse(Collection $collection): void
    {
        $collection->createSearchIndex([
            'mappings' => ['dynamic' => false],
        ]);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/#create-a-vector-search-index */
    public function createVectorSearchIndex(Collection $collection): void
    {
        $collection->createSearchIndex(
            [
                'fields' => [
                    [
                        'type' => 'vector',
                        'numDimensions' => 1,
                        'path' => 'genre',
                        'similarity' => 'cosine',
                    ],
                ],
            ],
            ['name' => 'vectorSearchIndex01', 'type' => 'vectorSearch'],
        );
    }

    /** @see https://www.mongodb.com/docs/manual/reference/method/db.collection.updateSearchIndex/#example */
    public function updateSearchIndexWithStoredSourceExclude(Collection $collection): void
    {
        $collection->createSearchIndex(
            [
                'mappings' => ['dynamic' => true],
                'storedSource' => ['exclude' => ['imdb.rating']],
            ],
            ['name' => 'searchIndex01'],
        );

        $collection->updateSearchIndex(
            'searchIndex01',
            [
                'mappings' => ['dynamic' => true],
                'storedSource' => ['exclude' => ['movies']],
            ],
        );
    }

    /** @see https://www.mongodb.com/docs/atlas/atlas-search/stored-source-definition/ */
    public function searchIndexStoredSourceTrue(Collection $collection): void
    {
        $collection->createSearchIndex([
            'mappings' => ['dynamic' => true],
            'storedSource' => true,
        ]);
    }

    /** @see https://www.mongodb.com/docs/atlas/atlas-search/stored-source-definition/ */
    public function searchIndexStoredSourceInclude(Collection $collection): void
    {
        $collection->createSearchIndex([
            'mappings' => ['dynamic' => true],
            'storedSource' => ['include' => ['title', 'awards.wins']],
        ]);
    }

    /** @see https://www.mongodb.com/docs/atlas/atlas-search/stored-source-definition/ */
    public function searchIndexStoredSourceExclude(Collection $collection): void
    {
        $collection->createSearchIndex([
            'mappings' => ['dynamic' => true],
            'storedSource' => ['exclude' => ['directors', 'imdb.rating']],
        ]);
    }

    /** @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-type/ */
    public function vectorSearchIndexStoredSourceTrue(Collection $collection): void
    {
        $collection->createSearchIndex(
            [
                'fields' => [
                    [
                        'type' => 'vector',
                        'path' => 'plot_embedding',
                        'numDimensions' => 1536,
                        'similarity' => 'euclidean',
                    ],
                ],
                'storedSource' => true,
            ],
            ['name' => 'my_vector_index', 'type' => 'vectorSearch'],
        );
    }

    /** @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-type/ */
    public function vectorSearchIndexStoredSourceInclude(Collection $collection): void
    {
        $collection->createSearchIndex(
            [
                'fields' => [
                    [
                        'type' => 'vector',
                        'path' => 'plot_embedding',
                        'numDimensions' => 1536,
                        'similarity' => 'euclidean',
                    ],
                    [
                        'type' => 'filter',
                        'path' => 'genres',
                    ],
                ],
                'storedSource' => ['include' => ['title', 'plot_embedding']],
            ],
            ['name' => 'my_vector_index', 'type' => 'vectorSearch'],
        );
    }
}
