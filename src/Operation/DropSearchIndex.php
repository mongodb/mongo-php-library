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
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for the dropSearchIndexes command.
 *
 * @see \MongoDB\Collection::dropSearchIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/dropSearchIndexes/
 */
final class DropSearchIndex
{
    private const ERROR_CODE_NAMESPACE_NOT_FOUND = 26;

    /**
     * Constructs a dropSearchIndex command.
     *
     * @param string                 $databaseName   Database name
     * @param string                 $collectionName Collection name
     * @param string                 $name           Index name
     * @param array{comment?: mixed} $options        Command options
     * @throws InvalidArgumentException for parameter parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, private string $name, private array $options = [])
    {
        if ($name === '') {
            throw new InvalidArgumentException('Index name cannot be empty');
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): void
    {
        $cmd = [
            'dropSearchIndex' => $this->collectionName,
            'name' => $this->name,
        ];

        if (isset($this->options['comment'])) {
            $cmd['comment'] = $this->options['comment'];
        }

        try {
            $server->executeCommand($this->databaseName, new Command($cmd));
        } catch (CommandException $e) {
            // Drop operations are idempotent. The server may return an error if the collection does not exist.
            if ($e->getCode() !== self::ERROR_CODE_NAMESPACE_NOT_FOUND) {
                throw $e;
            }
        }
    }
}
