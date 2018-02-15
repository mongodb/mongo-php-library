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

    private $databaseName;
    private $explainable;
    private $options;

    /* The Explainable knows what collection it targets, so we only need
     * a database to run `explain` on. Alternatively, we might decide to also
     * pull the database from Explainable somehow, since all Operations we
     * might explain also require a database name.
     *
     * Options will at least be verbosity (the only documented explain option)
     * and typeMap, which we can apply to the cursor before the execute()
     * method returns current($cursor->toArray()) or $cursor->toArray()[0]
     * (both are equivalent). */
    public function __construct($databaseName, Explainable $explainable, array $options = [])
    {
        $this->databaseName = $databaseName;
        $this->explainable = $explainable;
        $this->options = $options;
    }

    public function execute(Server $server)
    {
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
}
