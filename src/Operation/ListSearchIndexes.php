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

namespace MongoDB\Operation;

use Countable;
use Iterator;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\CachingIterator;

use function array_intersect_key;
use function is_string;

/**
 * Operation for the listSearchIndexes command.
 *
 * @see \MongoDB\Collection::listSearchIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/listSearchIndexes/
 */
final class ListSearchIndexes
{
    private array $listSearchIndexesOptions;
    private array $aggregateOptions;
    private Aggregate $aggregate;

    /**
     * Constructs an aggregate command for listing Atlas Search indexes
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     */
    public function __construct(private string $databaseName, private string $collectionName, array $options = [])
    {
        if (isset($options['name']) && ! is_string($options['name'])) {
            throw InvalidArgumentException::invalidType('"name" option', $options['name'], 'string');
        }

        if (isset($options['name']) && $options['name'] === '') {
            throw new InvalidArgumentException('"name" option cannot be empty');
        }

        $this->listSearchIndexesOptions = array_intersect_key($options, ['name' => 1]);
        $this->aggregateOptions = array_intersect_key($options, ['batchSize' => 1, 'codec' => 1, 'collation' => 1, 'comment' => 1, 'maxTimeMS' => 1, 'readConcern' => 1, 'readPreference' => 1, 'session' => 1, 'typeMap' => 1]);

        $this->aggregate = $this->createAggregate();
    }

    /**
     * Execute the operation.
     *
     * @return Iterator&Countable
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if collation or read concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): Iterator
    {
        $cursor = $this->aggregate->execute($server);

        return new CachingIterator($cursor);
    }

    private function createAggregate(): Aggregate
    {
        $pipeline = [
            ['$listSearchIndexes' => (object) $this->listSearchIndexesOptions],
        ];

        return new Aggregate($this->databaseName, $this->collectionName, $pipeline, $this->aggregateOptions);
    }
}
