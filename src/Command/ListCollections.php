<?php
/*
 * Copyright 2020-present MongoDB, Inc.
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

namespace MongoDB\Command;

use MongoDB\Driver\Command;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;

use function is_bool;
use function is_integer;
use function MongoDB\is_document;

/**
 * Wrapper for the listCollections command.
 *
 * @internal
 * @see https://mongodb.com/docs/manual/reference/command/listCollections/
 */
final class ListCollections
{
    /**
     * Constructs a listCollections command.
     *
     * Supported options:
     *
     *  * authorizedCollections (boolean): Determines which collections are
     *    returned based on the user privileges.
     *
     *    For servers < 4.0, this option is ignored.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * filter (document): Query by which to filter collections.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * nameOnly (boolean): A flag to indicate whether the command should
     *    return just the collection/view names and type or return both the name
     *    and other information.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     * @param string $databaseName Database name
     * @param array  $options      Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private array $options = [])
    {
        if (isset($options['authorizedCollections']) && ! is_bool($options['authorizedCollections'])) {
            throw InvalidArgumentException::invalidType('"authorizedCollections" option', $options['authorizedCollections'], 'boolean');
        }

        if (isset($options['filter']) && ! is_document($options['filter'])) {
            throw InvalidArgumentException::expectedDocumentType('"filter" option', $options['filter']);
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['nameOnly']) && ! is_bool($options['nameOnly'])) {
            throw InvalidArgumentException::invalidType('"nameOnly" option', $options['nameOnly'], 'boolean');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }
    }

    /**
     * Execute the operation.
     *
     * @return CursorInterface<array>
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): CursorInterface
    {
        /** @var CursorInterface<array> $cursor */
        $cursor = $server->executeReadCommand($this->databaseName, $this->createCommand(), $this->createOptions());
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);

        return $cursor;
    }

    /**
     * Create the listCollections command.
     */
    private function createCommand(): Command
    {
        $cmd = ['listCollections' => 1];

        if (! empty($this->options['filter'])) {
            $cmd['filter'] = (object) $this->options['filter'];
        }

        foreach (['authorizedCollections', 'comment', 'maxTimeMS', 'nameOnly'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        return new Command($cmd);
    }

    /**
     * Create options for executing the command.
     *
     * Note: read preference is intentionally omitted, as the spec requires that
     * the command be executed on the primary.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executecommand.php
     */
    private function createOptions(): array
    {
        $options = [];

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }
}
