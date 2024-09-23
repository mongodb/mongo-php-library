<?php
/*
 * Copyright 2018-present MongoDB, Inc.
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
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

use function current;
use function is_array;
use function is_string;

/**
 * Operation for the explain command.
 *
 * @see \MongoDB\Collection::explain()
 * @see https://mongodb.com/docs/manual/reference/command/explain/
 */
final class Explain
{
    public const VERBOSITY_ALL_PLANS = 'allPlansExecution';
    public const VERBOSITY_EXEC_STATS = 'executionStats';
    public const VERBOSITY_QUERY = 'queryPlanner';

    /**
     * Constructs an explain command for explainable operations.
     *
     * Supported options:
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be used
     *    used for the returned command result document.
     *
     *  * verbosity (string): The mode in which the explain command will be run.
     *
     * @param string      $databaseName Database name
     * @param Explainable $explainable  Operation to explain
     * @param array       $options      Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private Explainable $explainable, private array $options = [])
    {
        if (isset($this->options['readPreference']) && ! $this->options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $this->options['readPreference'], ReadPreference::class);
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['typeMap']) && ! is_array($this->options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $this->options['typeMap'], 'array');
        }

        if (isset($this->options['verbosity']) && ! is_string($this->options['verbosity'])) {
            throw InvalidArgumentException::invalidType('"verbosity" option', $this->options['verbosity'], 'string');
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnsupportedException if the server does not support explaining the operation
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): array|object
    {
        $cursor = $server->executeCommand($this->databaseName, $this->createCommand(), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create the explain command.
     */
    private function createCommand(): Command
    {
        $cmd = ['explain' => $this->explainable->getCommandDocument()];

        foreach (['comment', 'verbosity'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        return new Command($cmd);
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executecommand.php
     */
    private function createOptions(): array
    {
        $options = [];

        if (isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }
}
