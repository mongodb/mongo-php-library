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
use MongoDB\Model\StreamProcessorInfo;
use MongoDB\Operation\StreamProcessing\CreateStreamProcessor;
use MongoDB\Operation\StreamProcessing\GetStreamProcessor;

use function strlen;

/**
 * Handle for managing stream processors in a workspace.
 *
 * Obtained via {@see StreamProcessingClient::streamProcessors()}.
 */
final class StreamProcessors
{
    public function __construct(private Manager $manager)
    {
    }

    /**
     * Create a new stream processor.
     *
     * @see CreateStreamProcessor::__construct() for supported options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function create(string $name, array $pipeline, array $options = []): void
    {
        if (strlen($name) < 1) {
            throw new InvalidArgumentException('$name is invalid: ' . $name);
        }

        $operation = new CreateStreamProcessor($name, $pipeline, $options);
        $server = select_server_for_write($this->manager, []);
        $operation->execute($server);
    }

    /**
     * Return a handle for the named stream processor.
     *
     * Does not imply that the processor currently exists on the server.
     */
    public function get(string $name): StreamProcessor
    {
        return new StreamProcessor($this->manager, $name);
    }

    /**
     * Return information about a single stream processor.
     *
     * Sends the `getStreamProcessor` command.
     */
    public function getInfo(string $name): StreamProcessorInfo
    {
        if (strlen($name) < 1) {
            throw new InvalidArgumentException('$name is invalid: ' . $name);
        }

        $operation = new GetStreamProcessor($name);
        $server = select_server_for_write($this->manager, []);

        return $operation->execute($server);
    }
}
