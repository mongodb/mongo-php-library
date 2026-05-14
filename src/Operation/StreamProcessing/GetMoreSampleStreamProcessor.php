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
use MongoDB\Model\StreamProcessorSamples;

use function current;
use function is_array;
use function is_int;
use function iterator_to_array;

/**
 * Operation for the getMoreSampleStreamProcessor command.
 *
 * This is the second phase of the custom two-phase sample cursor; it is NOT
 * implemented using the standard MongoDB getMore command.
 */
final class GetMoreSampleStreamProcessor
{
    /**
     * Supported options:
     *
     *  * batchSize (integer): Number of documents to return in the batch.
     *
     * @param string $name     Stream processor name
     * @param int    $cursorId The cursorId returned by startSampleStreamProcessor or a prior getMore call.
     * @param array  $options  Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $name, private int $cursorId, private array $options = [])
    {
        if (isset($options['batchSize']) && ! is_int($options['batchSize'])) {
            throw InvalidArgumentException::invalidType('"batchSize" option', $options['batchSize'], 'integer');
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnexpectedValueException if the response is malformed
     * @throws DriverRuntimeException   for driver errors (e.g. connection errors)
     */
    public function execute(Server $server): StreamProcessorSamples
    {
        $cursor = $server->executeCommand('admin', $this->createCommand());
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

        $response = current(iterator_to_array($cursor));
        if (! is_array($response) || ! isset($response['cursorId'])) {
            throw new UnexpectedValueException('getMoreSampleStreamProcessor command did not return a cursorId');
        }

        /* Dev-server deviation: some server builds use "messages" instead of
         * "nextBatch". Prefer the spec-defined "nextBatch" but fall back to
         * "messages" if present. */
        $batch = $response['nextBatch'] ?? $response['messages'] ?? [];
        if (! is_array($batch)) {
            $batch = [];
        }

        return new StreamProcessorSamples((int) $response['cursorId'], $batch);
    }

    private function createCommand(): Command
    {
        $cmd = [
            'getMoreSampleStreamProcessor' => $this->name,
            'cursorId' => $this->cursorId,
        ];

        if (isset($this->options['batchSize'])) {
            $cmd['batchSize'] = $this->options['batchSize'];
        }

        return new Command($cmd);
    }
}
