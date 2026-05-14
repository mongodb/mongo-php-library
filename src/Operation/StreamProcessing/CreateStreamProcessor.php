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

use function is_bool;
use function is_string;
use function MongoDB\is_document;

/**
 * Operation for the createStreamProcessor command.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-stream-processing/
 */
final class CreateStreamProcessor
{
    /**
     * Constructs a createStreamProcessor command.
     *
     * Supported options:
     *
     *  * dlq (document): Dead letter queue configuration.
     *
     *  * streamMetaFieldName (string): Field name used for stream metadata.
     *
     *  * tier (string): Compute tier (e.g. "SP2", "SP5", "SP10", "SP30", "SP50").
     *
     *  * failover (bool): Whether failover is enabled.
     *
     * @param string $name     Stream processor name
     * @param array  $pipeline Aggregation pipeline
     * @param array  $options  Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $name, private array $pipeline, private array $options = [])
    {
        if (isset($options['dlq']) && ! is_document($options['dlq'])) {
            throw InvalidArgumentException::invalidType('"dlq" option', $options['dlq'], 'document');
        }

        if (isset($options['streamMetaFieldName']) && ! is_string($options['streamMetaFieldName'])) {
            throw InvalidArgumentException::invalidType('"streamMetaFieldName" option', $options['streamMetaFieldName'], 'string');
        }

        if (isset($options['tier']) && ! is_string($options['tier'])) {
            throw InvalidArgumentException::invalidType('"tier" option', $options['tier'], 'string');
        }

        if (isset($options['failover']) && ! is_bool($options['failover'])) {
            throw InvalidArgumentException::invalidType('"failover" option', $options['failover'], 'bool');
        }
    }

    /**
     * Execute the operation.
     *
     * @throws DriverRuntimeException for driver errors (e.g. connection errors)
     */
    public function execute(Server $server): void
    {
        $server->executeCommand('admin', $this->createCommand());
    }

    private function createCommand(): Command
    {
        $cmd = [
            'createStreamProcessor' => $this->name,
            'pipeline' => $this->pipeline,
        ];

        $subOptions = [];

        foreach (['dlq', 'streamMetaFieldName', 'tier', 'failover'] as $key) {
            if (isset($this->options[$key])) {
                /** @psalm-suppress MixedAssignment */
                $subOptions[$key] = $this->options[$key];
            }
        }

        if ($subOptions !== []) {
            $cmd['options'] = (object) $subOptions;
        }

        return new Command($cmd);
    }
}
