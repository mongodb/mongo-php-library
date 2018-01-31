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

use MongoDB\ChangeStream;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for creating a change stream with the aggregate command.
 *
 * @api
 * @see \MongoDB\Collection::watch()
 * @see https://docs.mongodb.com/manual/changeStreams/
 */
class Watch implements Executable
{
    const FULL_DOCUMENT_DEFAULT = 'default';
    const FULL_DOCUMENT_UPDATE_LOOKUP = 'updateLookup';

    private $databaseName;
    private $collectionName;
    private $pipeline;
    private $options;
    private $manager;

    /**
     * Constructs an aggregate command for creating a change stream.
     *
     * Supported options:
     *
     *  * batchSize (integer): The number of documents to return per batch.
     *
     *  * collation (document): Specifies a collation.
     *
     *  * fullDocument (string): Determines whether the "fullDocument" field
     *    will be populated for update operations. By default, change streams
     *    only return the delta of fields during the update operation (via the
     *    "updateDescription" field). To additionally return the most current
     *    majority-committed version of the updated document, specify
     *    "updateLookup" for this option. Defaults to "default".
     *
     *    Insert and replace operations always include the "fullDocument" field
     *    and delete operations omit the field as the document no longer exists.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * maxAwaitTimeMS (integer): The maximum amount of time for the server to
     *    wait on new documents to satisfy a change stream query.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference. This
     *    will be used to select a new server when resuming. Defaults to a
     *    "primary" read preference.
     *
     *  * resumeAfter (document): Specifies the logical starting point for the
     *    new change stream.
     *
     * @param string         $databaseName   Database name
     * @param string         $collectionName Collection name
     * @param array          $pipeline       List of pipeline operations
     * @param array          $options        Command options
     * @param Manager        $manager        Manager instance from the driver
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(Manager $manager, $databaseName, $collectionName, array $pipeline, array $options = [])
    {
        $options += [
            'fullDocument' => self::FULL_DOCUMENT_DEFAULT,
            'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
        ];

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

        $this->manager = $manager;
        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->pipeline = $pipeline;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return ChangeStream
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if collation, read concern, or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $command = $this->createCommand();

        $cursor = $command->execute($server);

        return new ChangeStream($cursor, $this->createResumeCallable());
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
