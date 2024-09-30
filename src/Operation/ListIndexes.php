<?php
/*
 * Copyright 2015-present MongoDB, Inc.
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

use EmptyIterator;
use Iterator;
use MongoDB\Driver\Command;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\CachingIterator;
use MongoDB\Model\CallbackIterator;
use MongoDB\Model\IndexInfo;

use function is_integer;

/**
 * Operation for the listIndexes command.
 *
 * @see \MongoDB\Collection::listIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/listIndexes/
 */
final class ListIndexes
{
    private const ERROR_CODE_DATABASE_NOT_FOUND = 60;
    private const ERROR_CODE_NAMESPACE_NOT_FOUND = 26;

    /**
     * Constructs a listIndexes command.
     *
     * Supported options:
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, private array $options = [])
    {
        if (isset($this->options['maxTimeMS']) && ! is_integer($this->options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $this->options['maxTimeMS'], 'integer');
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }
    }

    /**
     * Execute the operation.
     *
     * @return Iterator<int, IndexInfo>
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): Iterator
    {
        $cmd = ['listIndexes' => $this->collectionName];

        foreach (['comment', 'maxTimeMS'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        try {
            /** @var CursorInterface<array> $cursor */
            $cursor = $server->executeReadCommand($this->databaseName, new Command($cmd), $this->createOptions());
            $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        } catch (CommandException $e) {
            /* The server may return an error if the collection does not exist.
             * Check for possible error codes (see: SERVER-20463) and return an
             * empty iterator instead of throwing.
             */
            if ($e->getCode() === self::ERROR_CODE_NAMESPACE_NOT_FOUND || $e->getCode() === self::ERROR_CODE_DATABASE_NOT_FOUND) {
                return new EmptyIterator();
            }

            throw $e;
        }

        return new CachingIterator(
            new CallbackIterator(
                $cursor,
                fn (array $indexInfo): IndexInfo => new IndexInfo($indexInfo),
            ),
        );
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
