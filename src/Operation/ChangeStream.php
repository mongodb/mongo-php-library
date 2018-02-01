<?php
/*
 * Copyright 2017 MongoDB, Inc.
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

use MongoDB\ChangeStream as ChangeStreamResult;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for creating a change stream with the aggregate command.
 *
 * @api
 * @see \MongoDB\Collection::changeStream()
 * @see http://docs.mongodb.org/manual/reference/command/changeStream/
 */
class ChangeStream implements Executable
{
    const FULL_DOCUMENT_DEFAULT = 'default';
    const FULL_DOCUMENT_UPDATE_LOOKUP = 'updateLookup';

    private $databaseName;
    private $collectionName;
    private $pipeline;
    private $options;
    private $manager;

    /**
     * Constructs a changeStream command.
     *
     * Supported options:
     *
     *  * fullDocument (string): Allowed values: ‘default’, ‘updateLookup’.
     *    Defaults to ‘default’.  When set to ‘updateLookup’, the change
     *    notification for partial updates will include both a delta describing
     *    the changes to the document, as well as a copy of the entire document
     *    that was changed from some time after the change occurred. For forward
     *    compatibility, a driver MUST NOT raise an error when a user provides
     *    an unknown value. The driver relies on the server to validate this
     *    option.
     *
     *  * resumeAfter (document): Specifies the logical starting point for the
     *    new change stream.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern. Note that a
     *    "majority" read concern is not compatible with the $out stage
     *
     *    This is not supported for server versions < 3.2 and will result in an
     *    exception at execution time if used.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * maxAwaitTimeMS (integer): The maximum amount of time for the server to
     *    wait on new documents to satisfy a change stream query.
     *
     *  * batchSize (integer): The number of documents to return per batch.
     *
     *    This option is sent only if the caller explicitly provides a value.
     *    The default is to not send a value.
     *
     *  * collation (document): Specifies a collation.
     *
     *    This option is sent only if the caller explicitly provides a value.
     *    The default is to not send a value.
     *
     * @param string         $databaseName   Database name
     * @param string         $collectionName Collection name
     * @param array          $pipeline       List of pipeline operations
     * @param array          $options        Command options
     * @param Manager        $manager        Manager instance from the driver
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, array $pipeline, array $options = [], Manager $manager)
    {
        if (isset($options['batchSize']) && ! is_integer($options['batchSize'])) {
            throw InvalidArgumentException::invalidType('"batchSize" option', $options['batchSize'], 'integer');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['maxAwaitTimeMS']) && ! is_integer($options['maxAwaitTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxAwaitTimeMS" option', $options['maxAwaitTimeMS'], 'integer');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], 'MongoDB\Driver\ReadConcern');
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }

        if (isset($options['resumeAfter'])) {
            if ( ! is_array($options['resumeAfter']) && ! is_object($options['resumeAfter'])) {
                throw InvalidArgumentException::invalidType('"resumeAfter" option', $options['resumeAfter'], 'array or object');
            }
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->pipeline = $pipeline;
        $this->options = $options;
        $this->manager = $manager;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return ChangeStreamResult
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if collation, read concern, or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $command = $this->createCommand();

        $cursor = $command->execute($server);

        return new ChangeStreamResult($cursor, $this->createResumeCallable());
    }

    private function createAggregateOptions()
    {
        $aggOptions = array_intersect_key($this->options, ['batchSize' => 1, 'collation' => 1, 'maxAwaitTimeMS' => 1]);
        if ( ! $aggOptions) {
            return [];
        }
        return $aggOptions;
    }

    private function createChangeStreamOptions()
    {
        $csOptions = array_intersect_key($this->options, ['fullDocument' => 1, 'resumeAfter' => 1]);
        if ( ! $csOptions) {
            return [];
        }
        return $csOptions;
    }

    /**
     * Create the aggregate pipeline with the changeStream command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $changeStreamArray = ['$changeStream' => $this->createChangeStreamOptions()];
        array_unshift($this->pipeline, $changeStreamArray);

        $cmd = new Aggregate($this->databaseName, $this->collectionName, $this->pipeline, $this->createAggregateOptions());

        return $cmd;
    }

    private function createResumeCallable()
    {
        array_shift($this->pipeline);
        return function($resumeToken = null) {
            // Select a server from manager using read preference option
            $server = $this->manager->selectServer($this->options['readPreference']);
            // Update $this->options['resumeAfter'] from $resumeToken arg
            if ($resumeToken !== null) {
                $this->options['resumeAfter'] = $resumeToken;
            }
            // Return $this->execute() with the newly selected server
            return $this->execute($server);
        };
    }
}
