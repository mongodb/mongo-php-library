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

use ArrayIterator;
use MongoDB\BSON\JavascriptInterface;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\MapReduceResult;
use stdClass;

use function assert;
use function current;
use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function is_string;
use function MongoDB\create_field_path_type_map;
use function MongoDB\document_to_array;
use function MongoDB\is_document;
use function MongoDB\is_mapreduce_output_inline;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Operation for the mapReduce command.
 *
 * @see \MongoDB\Collection::mapReduce()
 * @see https://mongodb.com/docs/manual/reference/command/mapReduce/
 * @psalm-import-type MapReduceCallable from MapReduceResult
 *
 * @final extending this class will not be supported in v2.0.0
 */
class MapReduce implements Executable
{
    private array|object|string $out;

    /**
     * Constructs a mapReduce command.
     *
     * Required arguments:
     *
     *  * map (MongoDB\BSON\Javascript): A JavaScript function that associates
     *    or "maps" a value with a key and emits the key and value pair.
     *
     *    Passing a Javascript instance with a scope is deprecated. Put all
     *    scope variables in the "scope" option of the MapReduce operation.
     *
     *  * reduce (MongoDB\BSON\Javascript): A JavaScript function that "reduces"
     *    to a single object all the values associated with a particular key.
     *
     *    Passing a Javascript instance with a scope is deprecated. Put all
     *    scope variables in the "scope" option of the MapReduce operation.
     *
     *  * out (string|document): Specifies where to output the result of the
     *    map-reduce operation. You can either output to a collection or return
     *    the result inline. On a primary member of a replica set you can output
     *    either to a collection or inline, but on a secondary, only inline
     *    output is possible.
     *
     * Supported options:
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation. This only applies when results
     *    are output to a collection.
     *
     *  * collation (document): Collation specification.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * finalize (MongoDB\BSON\JavascriptInterface): Follows the reduce method
     *    and modifies the output.
     *
     *    Passing a Javascript instance with a scope is deprecated. Put all
     *    scope variables in the "scope" option of the MapReduce operation.
     *
     *  * jsMode (boolean): Specifies whether to convert intermediate data into
     *    BSON format between the execution of the map and reduce functions.
     *
     *  * limit (integer): Specifies a maximum number of documents for the input
     *    into the map function.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * query (document): Specifies the selection criteria using query
     *    operators for determining the documents input to the map function.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern. This is not
     *    supported when results are returned inline.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *    This option is ignored if results are output to a collection.
     *
     *  * scope (document): Specifies global variables that are accessible in
     *    the map, reduce and finalize functions.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * sort (document): Sorts the input documents. This option is useful for
     *    optimization. For example, specify the sort key to be the same as the
     *    emit key so that there are fewer reduce operations. The sort key must
     *    be in an existing index for this collection.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     *  * verbose (boolean): Specifies whether to include the timing information
     *    in the result information.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern. This only
     *    applies when results are output to a collection.
     *
     * @param string              $databaseName   Database name
     * @param string              $collectionName Collection name
     * @param JavascriptInterface $map            Map function
     * @param JavascriptInterface $reduce         Reduce function
     * @param string|array|object $out            Output specification
     * @param array               $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, private JavascriptInterface $map, private JavascriptInterface $reduce, string|array|object $out, private array $options = [])
    {
        if (isset($this->options['bypassDocumentValidation']) && ! is_bool($this->options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $this->options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($this->options['collation']) && ! is_document($this->options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $this->options['collation']);
        }

        if (isset($this->options['finalize']) && ! $this->options['finalize'] instanceof JavascriptInterface) {
            throw InvalidArgumentException::invalidType('"finalize" option', $this->options['finalize'], JavascriptInterface::class);
        }

        if (isset($this->options['jsMode']) && ! is_bool($this->options['jsMode'])) {
            throw InvalidArgumentException::invalidType('"jsMode" option', $this->options['jsMode'], 'boolean');
        }

        if (isset($this->options['limit']) && ! is_integer($this->options['limit'])) {
            throw InvalidArgumentException::invalidType('"limit" option', $this->options['limit'], 'integer');
        }

        if (isset($this->options['maxTimeMS']) && ! is_integer($this->options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $this->options['maxTimeMS'], 'integer');
        }

        if (isset($this->options['query']) && ! is_document($this->options['query'])) {
            throw InvalidArgumentException::expectedDocumentType('"query" option', $this->options['query']);
        }

        if (isset($this->options['readConcern']) && ! $this->options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $this->options['readConcern'], ReadConcern::class);
        }

        if (isset($this->options['readPreference']) && ! $this->options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $this->options['readPreference'], ReadPreference::class);
        }

        if (isset($this->options['scope']) && ! is_document($this->options['scope'])) {
            throw InvalidArgumentException::expectedDocumentType('"scope" option', $this->options['scope']);
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['sort']) && ! is_document($this->options['sort'])) {
            throw InvalidArgumentException::expectedDocumentType('"sort" option', $this->options['sort']);
        }

        if (isset($this->options['typeMap']) && ! is_array($this->options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $this->options['typeMap'], 'array');
        }

        if (isset($this->options['verbose']) && ! is_bool($this->options['verbose'])) {
            throw InvalidArgumentException::invalidType('"verbose" option', $this->options['verbose'], 'boolean');
        }

        if (isset($this->options['writeConcern']) && ! $this->options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $this->options['writeConcern'], WriteConcern::class);
        }

        if (isset($this->options['bypassDocumentValidation']) && ! $this->options['bypassDocumentValidation']) {
            unset($this->options['bypassDocumentValidation']);
        }

        if (isset($this->options['readConcern']) && $this->options['readConcern']->isDefault()) {
            unset($this->options['readConcern']);
        }

        if (isset($this->options['writeConcern']) && $this->options['writeConcern']->isDefault()) {
            unset($this->options['writeConcern']);
        }

        // Handle deprecation of CodeWScope
        if ($map->getScope() !== null) {
            @trigger_error('Use of Javascript with scope in "$map" argument for MapReduce is deprecated. Put all scope variables in the "scope" option of the MapReduce operation.', E_USER_DEPRECATED);
        }

        if ($reduce->getScope() !== null) {
            @trigger_error('Use of Javascript with scope in "$reduce" argument for MapReduce is deprecated. Put all scope variables in the "scope" option of the MapReduce operation.', E_USER_DEPRECATED);
        }

        if (isset($this->options['finalize']) && $this->options['finalize']->getScope() !== null) {
            @trigger_error('Use of Javascript with scope in "finalize" option for MapReduce is deprecated. Put all scope variables in the "scope" option of the MapReduce operation.', E_USER_DEPRECATED);
        }

        $this->checkOutDeprecations($out);

        $this->out = $out;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return MapReduceResult
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if read concern or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction) {
            if (isset($this->options['readConcern'])) {
                throw UnsupportedException::readConcernNotSupportedInTransaction();
            }

            if (isset($this->options['writeConcern'])) {
                throw UnsupportedException::writeConcernNotSupportedInTransaction();
            }
        }

        $hasOutputCollection = ! is_mapreduce_output_inline($this->out);

        $command = $this->createCommand();
        $options = $this->createOptions($hasOutputCollection);

        /* If the mapReduce operation results in a write, use
         * executeReadWriteCommand to ensure we're handling the writeConcern
         * option.
         * In other cases, we use executeCommand as this will prevent the
         * mapReduce operation from being retried when retryReads is enabled.
         * See https://github.com/mongodb/specifications/blob/master/source/retryable-reads/retryable-reads.rst#unsupported-read-operations. */
        $cursor = $hasOutputCollection
            ? $server->executeReadWriteCommand($this->databaseName, $command, $options)
            : $server->executeCommand($this->databaseName, $command, $options);

        if (isset($this->options['typeMap']) && ! $hasOutputCollection) {
            $cursor->setTypeMap(create_field_path_type_map($this->options['typeMap'], 'results.$'));
        }

        $result = current($cursor->toArray());
        assert($result instanceof stdClass);

        $getIterator = $this->createGetIteratorCallable($result, $server);

        return new MapReduceResult($getIterator, $result);
    }

    private function checkOutDeprecations(string|array|object $out): void
    {
        if (is_string($out)) {
            return;
        }

        $out = document_to_array($out);

        if (isset($out['nonAtomic']) && ! $out['nonAtomic']) {
            @trigger_error('Specifying false for "out.nonAtomic" is deprecated.', E_USER_DEPRECATED);
        }

        if (isset($out['sharded']) && ! $out['sharded']) {
            @trigger_error('Specifying false for "out.sharded" is deprecated.', E_USER_DEPRECATED);
        }
    }

    /**
     * Create the mapReduce command.
     */
    private function createCommand(): Command
    {
        $cmd = [
            'mapReduce' => $this->collectionName,
            'map' => $this->map,
            'reduce' => $this->reduce,
            'out' => $this->out,
        ];

        foreach (['bypassDocumentValidation', 'comment', 'finalize', 'jsMode', 'limit', 'maxTimeMS', 'verbose'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        foreach (['collation', 'query', 'scope', 'sort'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = (object) $this->options[$option];
            }
        }

        return new Command($cmd);
    }

    /**
     * Creates a callable for MapReduceResult::getIterator().
     *
     * @psalm-return MapReduceCallable
     * @throws UnexpectedValueException if the command response was malformed
     */
    private function createGetIteratorCallable(stdClass $result, Server $server): callable
    {
        // Inline results can be wrapped with an ArrayIterator
        if (isset($result->results) && is_array($result->results)) {
            $results = $result->results;

            return fn () => new ArrayIterator($results);
        }

        if (isset($result->result) && (is_string($result->result) || is_object($result->result))) {
            $options = isset($this->options['typeMap']) ? ['typeMap' => $this->options['typeMap']] : [];

            $find = is_string($result->result)
                ? new Find($this->databaseName, $result->result, [], $options)
                : new Find($result->result->db, $result->result->collection, [], $options);

            return fn () => $find->execute($server);
        }

        throw new UnexpectedValueException('mapReduce command did not return inline results or an output collection');
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executereadcommand.php
     * @see https://php.net/manual/en/mongodb-driver-server.executereadwritecommand.php
     */
    private function createOptions(bool $hasOutputCollection): array
    {
        $options = [];

        if (isset($this->options['readConcern'])) {
            $options['readConcern'] = $this->options['readConcern'];
        }

        if (! $hasOutputCollection && isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        if ($hasOutputCollection && isset($this->options['writeConcern'])) {
            $options['writeConcern'] = $this->options['writeConcern'];
        }

        return $options;
    }
}
