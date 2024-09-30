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

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\SearchIndexInput;

use function array_column;
use function array_is_list;
use function current;
use function is_array;
use function sprintf;

/**
 * Operation for the createIndexes command.
 *
 * @see \MongoDB\Collection::createSearchIndex()
 * @see \MongoDB\Collection::createSearchIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/createSearchIndexes/
 */
final class CreateSearchIndexes
{
    private array $indexes = [];

    /**
     * Constructs a createSearchIndexes command.
     *
     * @param string                 $databaseName   Database name
     * @param string                 $collectionName Collection name
     * @param array[]                $indexes        List of search index specifications
     * @param array{comment?: mixed} $options        Command options
     * @throws InvalidArgumentException for parameter parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, array $indexes, private array $options)
    {
        if (! array_is_list($indexes)) {
            throw new InvalidArgumentException('$indexes is not a list');
        }

        foreach ($indexes as $i => $index) {
            if (! is_array($index)) {
                throw InvalidArgumentException::invalidType(sprintf('$indexes[%d]', $i), $index, 'array');
            }

            $this->indexes[] = new SearchIndexInput($index);
        }
    }

    /**
     * Execute the operation.
     *
     * @return string[] The names of the created indexes
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): array
    {
        $cmd = [
            'createSearchIndexes' => $this->collectionName,
            'indexes' => $this->indexes,
        ];

        if (isset($this->options['comment'])) {
            $cmd['comment'] = $this->options['comment'];
        }

        $cursor = $server->executeCommand($this->databaseName, new Command($cmd));

        /** @var object{indexesCreated: list<object{name: string}>} $result */
        $result = current($cursor->toArray());

        return array_column($result->indexesCreated, 'name');
    }
}
