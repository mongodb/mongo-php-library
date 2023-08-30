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

use function MongoDB\is_document;

/**
 * Operation for the createIndexes command.
 *
 * @see \MongoDB\Collection::updateSearchIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/updateSearchIndexes/
 */
class UpdateSearchIndex implements Executable
{
    private string $databaseName;
    private string $collectionName;
    private string $name;
    private object $definition;
    private array $options = [];

    /**
     * Constructs a createSearchIndexes command.
     *
     * @param string                 $databaseName   Database name
     * @param string                 $collectionName Collection name
     * @param string                 $name           Search index name
     * @param array|object           $definition     Atlas Search index definition
     * @param array{comment?: mixed} $options        Command options
     * @throws InvalidArgumentException for parameter parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, string $name, $definition, array $options = [])
    {
        if ($name === '') {
            throw new InvalidArgumentException('Index name cannot be empty');
        }

        if (! is_document($definition)) {
            throw InvalidArgumentException::expectedDocumentType('$definition', $definition);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->name = $name;
        $this->definition = (object) $definition;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): void
    {
        $cmd = [
            'updateSearchIndex' => $this->collectionName,
            'name' => $this->name,
            'definition' => $this->definition,
        ];

        if (isset($this->options['comment'])) {
            $cmd['comment'] = $this->options['comment'];
        }

        $server->executeCommand($this->databaseName, new Command($cmd));
    }
}
