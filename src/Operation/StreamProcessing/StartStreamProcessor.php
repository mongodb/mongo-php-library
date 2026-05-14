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

use MongoDB\BSON\Timestamp;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;

use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;
use function sprintf;

/**
 * Operation for the startStreamProcessor command.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-stream-processing/
 */
final class StartStreamProcessor
{
    private const VALID_FAILOVER_MODES = ['GRACEFUL', 'FORCED'];

    /**
     * Constructs a startStreamProcessor command.
     *
     * Supported options:
     *
     *  * workers (integer): Number of workers.
     *
     *  * clearCheckpoints (bool): Clear checkpoints before starting.
     *
     *  * startAtOperationTime (MongoDB\BSON\Timestamp): Resume from a specific
     *    operation time.
     *
     *  * tier (string): Compute tier. One of "SP2", "SP5", "SP10", "SP30", "SP50".
     *
     *  * enableAutoScaling (bool): Enable auto-scaling.
     *
     *  * failover (array): Failover configuration. The "region" key is required.
     *    Optional "mode" must be "GRACEFUL" (default) or "FORCED". Optional
     *    "dryRun" validates without executing.
     *
     * Note: the spec's "startAfter" option is RESERVED for future use and is not
     * yet accepted by the server; this driver MUST NOT send it.
     *
     * @param string $name    Stream processor name
     * @param array  $options Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $name, private array $options = [])
    {
        if (isset($options['workers']) && ! is_int($options['workers'])) {
            throw InvalidArgumentException::invalidType('"workers" option', $options['workers'], 'integer');
        }

        if (isset($options['clearCheckpoints']) && ! is_bool($options['clearCheckpoints'])) {
            throw InvalidArgumentException::invalidType('"clearCheckpoints" option', $options['clearCheckpoints'], 'bool');
        }

        if (isset($options['startAtOperationTime']) && ! $options['startAtOperationTime'] instanceof Timestamp) {
            throw InvalidArgumentException::invalidType('"startAtOperationTime" option', $options['startAtOperationTime'], Timestamp::class);
        }

        if (isset($options['tier']) && ! is_string($options['tier'])) {
            throw InvalidArgumentException::invalidType('"tier" option', $options['tier'], 'string');
        }

        if (isset($options['enableAutoScaling']) && ! is_bool($options['enableAutoScaling'])) {
            throw InvalidArgumentException::invalidType('"enableAutoScaling" option', $options['enableAutoScaling'], 'bool');
        }

        if (isset($options['failover'])) {
            self::validateFailover($options['failover']);
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
        $cmd = ['startStreamProcessor' => $this->name];

        if (isset($this->options['workers'])) {
            $cmd['workers'] = $this->options['workers'];
        }

        $subOptions = [];

        foreach (['clearCheckpoints', 'startAtOperationTime', 'tier', 'enableAutoScaling'] as $key) {
            if (isset($this->options[$key])) {
                $subOptions[$key] = $this->options[$key];
            }
        }

        if ($subOptions !== []) {
            $cmd['options'] = (object) $subOptions;
        }

        if (isset($this->options['failover'])) {
            $cmd['failover'] = (object) $this->options['failover'];
        }

        return new Command($cmd);
    }

    private static function validateFailover(mixed $failover): void
    {
        if (! is_array($failover)) {
            throw InvalidArgumentException::invalidType('"failover" option', $failover, 'array');
        }

        if (! isset($failover['region']) || ! is_string($failover['region'])) {
            throw new InvalidArgumentException('"failover" option requires a "region" string');
        }

        if (isset($failover['mode'])) {
            if (! is_string($failover['mode'])) {
                throw InvalidArgumentException::invalidType('"failover.mode" option', $failover['mode'], 'string');
            }

            if (! in_array($failover['mode'], self::VALID_FAILOVER_MODES, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid "failover.mode" value "%s"; expected one of: %s',
                    $failover['mode'],
                    implode(', ', self::VALID_FAILOVER_MODES),
                ));
            }
        }

        if (isset($failover['dryRun']) && ! is_bool($failover['dryRun'])) {
            throw InvalidArgumentException::invalidType('"failover.dryRun" option', $failover['dryRun'], 'bool');
        }
    }
}
