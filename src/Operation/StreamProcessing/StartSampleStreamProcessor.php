<?php
/*
 * Copyright 2026-present MongoDB, Inc.
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

namespace MongoDB\Operation\StreamProcessing;

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;

use function current;
use function is_array;
use function is_int;
use function iterator_to_array;

/**
 * Operation for the startSampleStreamProcessor command.
 *
 * Returns the int64 cursorId used by GetMoreSampleStreamProcessor to retrieve
 * the first batch of sampled documents. The startSampleStreamProcessor command
 * itself does not return any documents.
 */
final class StartSampleStreamProcessor
{
    /**
     * Supported options:
     *
     *  * limit (integer): Maximum number of documents to sample.
     *
     * @param string $name    Stream processor name
     * @param array  $options Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $name, private array $options = [])
    {
        if (isset($options['limit']) && ! is_int($options['limit'])) {
            throw InvalidArgumentException::invalidType('"limit" option', $options['limit'], 'integer');
        }
    }

    /**
     * Execute the operation.
     *
     * @return int The cursorId returned by the server.
     * @throws UnexpectedValueException if the response is missing cursorId
     * @throws DriverRuntimeException   for driver errors (e.g. connection errors)
     */
    public function execute(Server $server): int
    {
        $cursor = $server->executeCommand('admin', $this->createCommand());
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

        $response = current(iterator_to_array($cursor));
        if (! is_array($response) || ! isset($response['cursorId'])) {
            throw new UnexpectedValueException('startSampleStreamProcessor command did not return a cursorId');
        }

        return (int) $response['cursorId'];
    }

    private function createCommand(): Command
    {
        $cmd = ['startSampleStreamProcessor' => $this->name];

        if (isset($this->options['limit'])) {
            /** @psalm-suppress MixedAssignment */
            $cmd['limit'] = $this->options['limit'];
        }

        return new Command($cmd);
    }
}
