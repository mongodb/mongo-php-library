<?php
/*
 * Copyright 2018 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;

/**
 * Operation for the explain command.
 *
 * @api
 * @see \MongoDB\Collection::explain()
 * @see http://docs.mongodb.org/manual/reference/command/explain/
 */
class Explain implements Executable
{
    const VERBOSITY_ALL_PLANS = 'allPlansExecution';
    const VERBOSITY_EXEC_STATS = 'executionStats';
    const VERBOSITY_QUERY = 'queryPlanner';

    private static $wireVersionForExplain = 2;
    private static $wireVersionForDistinct = 4;
    private static $wireVersionForFindAndModify = 4;

    private $databaseName;
    private $explainable;
    private $options;

    /**
     * Constructs an explain command for explainable operations.
     *
     * Supported options:
     *
     *  * verbosity (string): The mode in which the explain command will be run.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be used
     *    used for the returned command result document.
     *
     * @param string $databaseName      Database name
     * @param Explainable $explainable  Operation to explain
     * @param array  $options           Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, Explainable $explainable, array $options = [])
    {
        if (isset($options['verbosity']) && ! is_string($options['verbosity'])) {
            throw InvalidArgumentException::invalidType('"verbosity" option', $options['verbosity'], 'string');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        $this->databaseName = $databaseName;
        $this->explainable = $explainable;
        $this->options = $options;
    }

    public function execute(Server $server)
    {
        if (! \MongoDB\server_supports_feature($server, self::$wireVersionForExplain)) {
            throw UnsupportedException::explainNotSupported();
        }

        if ($this->explainable instanceof Distinct && ! \MongoDB\server_supports_feature($server, self::$wireVersionForDistinct)) {
            throw UnsupportedException::explainNotSupported();
        }

        if ($this->isFindAndModify($this->explainable) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForFindAndModify)) {
            throw UnsupportedException::explainNotSupported();
        }

        $cmd = ['explain' => $this->explainable->getCommandDocument()];

        if (isset($this->options['verbosity'])) {
            $cmd['verbosity'] = $this->options['verbosity'];
        }

        $cursor = $server->executeCommand($this->databaseName, new Command($cmd));

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    private function isFindAndModify($explainable)
    {
        if ($explainable instanceof FindAndModify || $explainable instanceof FindOneAndDelete || $explainable instanceof FindOneAndReplace || $explainable instanceof FindOneAndUpdate) {
            return true;
        }
        return false;
    }
}
