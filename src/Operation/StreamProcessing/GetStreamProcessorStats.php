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
use function is_bool;
use function iterator_to_array;

/**
 * Operation for the getStreamProcessorStats command.
 */
final class GetStreamProcessorStats
{
    /**
     * Supported options:
     *
     *  * verbose (bool): If true, includes per-operator statistics.
     *
     * @param string $name    Stream processor name
     * @param array  $options Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $name, private array $options = [])
    {
        if (isset($options['verbose']) && ! is_bool($options['verbose'])) {
            throw InvalidArgumentException::invalidType('"verbose" option', $options['verbose'], 'bool');
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnexpectedValueException if the server response is malformed
     * @throws DriverRuntimeException   for driver errors (e.g. connection errors)
     */
    public function execute(Server $server): array
    {
        $cursor = $server->executeReadCommand('admin', $this->createCommand());
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

        $response = current(iterator_to_array($cursor));
        if (! is_array($response)) {
            throw new UnexpectedValueException('getStreamProcessorStats command returned no response');
        }

        return $response;
    }

    private function createCommand(): Command
    {
        $cmd = ['getStreamProcessorStats' => $this->name];

        if (isset($this->options['verbose'])) {
            $cmd['options'] = (object) ['verbose' => $this->options['verbose']];
        }

        return new Command($cmd);
    }
}
