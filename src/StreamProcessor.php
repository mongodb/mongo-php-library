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

namespace MongoDB;

use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\StreamProcessorSamples;
use MongoDB\Operation\StreamProcessing\DropStreamProcessor;
use MongoDB\Operation\StreamProcessing\GetMoreSampleStreamProcessor;
use MongoDB\Operation\StreamProcessing\GetStreamProcessorStats;
use MongoDB\Operation\StreamProcessing\StartSampleStreamProcessor;
use MongoDB\Operation\StreamProcessing\StartStreamProcessor;
use MongoDB\Operation\StreamProcessing\StopStreamProcessor;

use function array_diff_key;
use function strlen;

/**
 * Handle for a specific named stream processor.
 *
 * Holding a handle does not imply the processor currently exists on the server.
 */
final class StreamProcessor
{
    /**
     * @param Manager $manager Driver manager bound to a workspace endpoint
     * @param string  $name    Stream processor name
     * @throws InvalidArgumentException if the name is empty
     */
    public function __construct(private Manager $manager, private string $name)
    {
        if (strlen($name) < 1) {
            throw new InvalidArgumentException('$name is invalid: ' . $name);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Start the processor.
     *
     * @see StartStreamProcessor::__construct() for supported options
     */
    public function start(array $options = []): void
    {
        $operation = new StartStreamProcessor($this->name, $options);
        $server = select_server_for_write($this->manager, []);
        $operation->execute($server);
    }

    /**
     * Stop the processor. The processor remains in STOPPED state and can be restarted.
     */
    public function stop(): void
    {
        $operation = new StopStreamProcessor($this->name);
        $server = select_server_for_write($this->manager, []);
        $operation->execute($server);
    }

    /**
     * Drop the processor permanently. A dropped processor cannot be recovered.
     */
    public function drop(): void
    {
        $operation = new DropStreamProcessor($this->name);
        $server = select_server_for_write($this->manager, []);
        $operation->execute($server);
    }

    /**
     * Return runtime statistics for the processor.
     *
     * @see GetStreamProcessorStats::__construct() for supported options
     */
    public function stats(array $options = []): array
    {
        $operation = new GetStreamProcessorStats($this->name, $options);
        $server = select_server_for_write($this->manager, []);

        return $operation->execute($server);
    }

    /**
     * Retrieve a batch of sampled documents.
     *
     * Routes to startSampleStreamProcessor when no cursorId is supplied (or
     * cursorId is 0); otherwise routes to getMoreSampleStreamProcessor with the
     * supplied cursorId. The caller MUST stop iterating when the returned
     * cursorId is 0.
     *
     * Supported options:
     *
     *  * cursorId (integer): The cursor id from a prior call. Absent or 0 opens
     *    a new sample cursor.
     *
     *  * limit (integer): Maximum documents to sample. Only sent on the initial
     *    call; ignored on subsequent calls.
     *
     *  * batchSize (integer): Batch size. Only sent on subsequent calls;
     *    ignored on the initial call.
     */
    public function getStreamProcessorSamples(array $options = []): StreamProcessorSamples
    {
        $cursorId = isset($options['cursorId']) ? (int) $options['cursorId'] : 0;
        $server = select_server_for_write($this->manager, []);

        if ($cursorId === 0) {
            $startOptions = array_diff_key($options, ['cursorId' => 1, 'batchSize' => 1]);
            $operation = new StartSampleStreamProcessor($this->name, $startOptions);
            $newCursorId = $operation->execute($server);

            return new StreamProcessorSamples($newCursorId, []);
        }

        $getMoreOptions = array_diff_key($options, ['cursorId' => 1, 'limit' => 1]);
        $operation = new GetMoreSampleStreamProcessor($this->name, $cursorId, $getMoreOptions);

        return $operation->execute($server);
    }
}
