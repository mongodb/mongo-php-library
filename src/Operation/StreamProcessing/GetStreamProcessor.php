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
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\StreamProcessorInfo;

use function current;
use function is_array;
use function iterator_to_array;

/**
 * Operation for the getStreamProcessor command.
 */
final class GetStreamProcessor
{
    public function __construct(private string $name)
    {
    }

    /**
     * Execute the operation.
     *
     * @throws UnexpectedValueException if the server response is malformed
     * @throws DriverRuntimeException   for driver errors (e.g. connection errors)
     */
    public function execute(Server $server): StreamProcessorInfo
    {
        $cursor = $server->executeReadCommand('admin', new Command(['getStreamProcessor' => $this->name]));
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

        $response = current(iterator_to_array($cursor));
        if (! is_array($response)) {
            throw new UnexpectedValueException('getStreamProcessor command returned no response');
        }

        /* Dev-server deviation: some server builds wrap the processor document in
         * a top-level "result" key. Unwrap if present so the rest of the driver
         * sees a flat document matching the spec. */
        if (isset($response['result']) && is_array($response['result'])) {
            $response = $response['result'] + $response;
            unset($response['result']);
        }

        return new StreamProcessorInfo($response);
    }
}
