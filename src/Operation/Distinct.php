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

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;

use function current;
use function is_array;
use function is_integer;
use function is_object;
use function is_string;
use function MongoDB\create_field_path_type_map;
use function MongoDB\is_document;

/**
 * Operation for the distinct command.
 *
 * @see \MongoDB\Collection::distinct()
 * @see https://mongodb.com/docs/manual/reference/command/distinct/
 */
final class Distinct implements Explainable
{
    /**
     * Constructs a distinct command.
     *
     * Supported options:
     *
     *  * collation (document): Collation specification.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for server versions < 4.4.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *    This is not supported for server versions < 7.1.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * typeMap (array): Type map for BSON deserialization.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param string       $fieldName      Field for which to return distinct values
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, private string $fieldName, private array|object $filter = [], private array $options = [])
    {
        if (! is_document($filter)) {
            throw InvalidArgumentException::expectedDocumentType('$filter', $filter);
        }

        if (isset($this->options['collation']) && ! is_document($this->options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $this->options['collation']);
        }

        if (isset($this->options['hint']) && ! is_string($this->options['hint']) && ! is_document($this->options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $this->options['hint']);
        }

        if (isset($this->options['maxTimeMS']) && ! is_integer($this->options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $this->options['maxTimeMS'], 'integer');
        }

        if (isset($this->options['readConcern']) && ! $this->options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $this->options['readConcern'], ReadConcern::class);
        }

        if (isset($this->options['readPreference']) && ! $this->options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $this->options['readPreference'], ReadPreference::class);
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['typeMap']) && ! is_array($this->options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $this->options['typeMap'], 'array');
        }

        if (isset($this->options['readConcern']) && $this->options['readConcern']->isDefault()) {
            unset($this->options['readConcern']);
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if read concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): array
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['readConcern'])) {
            throw UnsupportedException::readConcernNotSupportedInTransaction();
        }

        $cursor = $server->executeReadCommand($this->databaseName, new Command($this->createCommandDocument()), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap(create_field_path_type_map($this->options['typeMap'], 'values.$'));
        }

        $result = current($cursor->toArray());

        if (! is_object($result) || ! isset($result->values) || ! is_array($result->values)) {
            throw new UnexpectedValueException('distinct command did not return a "values" array');
        }

        return $result->values;
    }

    /**
     * Returns the command document for this operation.
     *
     * @see Explainable::getCommandDocument()
     */
    public function getCommandDocument(): array
    {
        $cmd = $this->createCommandDocument();

        // Read concern can change the query plan
        if (isset($this->options['readConcern'])) {
            $cmd['readConcern'] = $this->options['readConcern'];
        }

        return $cmd;
    }

    /**
     * Create the distinct command document.
     */
    private function createCommandDocument(): array
    {
        $cmd = [
            'distinct' => $this->collectionName,
            'key' => $this->fieldName,
        ];

        if ($this->filter !== []) {
            $cmd['query'] = (object) $this->filter;
        }

        if (isset($this->options['collation'])) {
            $cmd['collation'] = (object) $this->options['collation'];
        }

        if (isset($this->options['hint'])) {
            /** @psalm-var string|object */
            $cmd['hint'] = is_array($this->options['hint']) ? (object) $this->options['hint'] : $this->options['hint'];
        }

        foreach (['comment', 'maxTimeMS'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        return $cmd;
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executereadcommand.php
     */
    private function createOptions(): array
    {
        $options = [];

        if (isset($this->options['readConcern'])) {
            $options['readConcern'] = $this->options['readConcern'];
        }

        if (isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }
}
