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

use Iterator;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\CodecCursor;
use stdClass;

use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function is_string;
use function MongoDB\is_document;
use function MongoDB\is_last_pipeline_operator_write;
use function MongoDB\is_pipeline;

/**
 * Operation for the aggregate command.
 *
 * @see \MongoDB\Collection::aggregate()
 * @see https://mongodb.com/docs/manual/reference/command/aggregate/
 */
class Aggregate implements Executable, Explainable
{
    private string $databaseName;

    private ?string $collectionName = null;

    private array $pipeline;

    private array $options;

    private bool $isWrite;

    /**
     * Constructs an aggregate command.
     *
     * Supported options:
     *
     *  * allowDiskUse (boolean): Enables writing to temporary files. When set
     *    to true, aggregation stages can write data to the _tmp sub-directory
     *    in the dbPath directory.
     *
     *  * batchSize (integer): The number of documents to return per batch.
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation. This only applies when an $out
     *    or $merge stage is specified.
     *
     *  * codec (MongoDB\Codec\DocumentCodec): Codec used to decode documents
     *    from BSON to PHP objects.
     *
     *  * collation (document): Collation specification.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    Only string values are supported for server versions < 4.4.
     *
     *  * explain (boolean): Specifies whether or not to return the information
     *    on the processing of the pipeline.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *  * let (document): Map of parameter names and values. Values must be
     *    constant or closed expressions that do not reference document fields.
     *    Parameters can then be accessed as variables in an aggregate
     *    expression context (e.g. "$$var").
     *
     *    This is not supported for server versions < 5.0 and will result in an
     *    exception at execution time if used.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *    This option is ignored if an $out or $merge stage is specified.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern. This only
     *    applies when an $out or $merge stage is specified.
     *
     * Note: Collection-agnostic commands (e.g. $currentOp) may be executed by
     * specifying null for the collection name.
     *
     * @param string      $databaseName   Database name
     * @param string|null $collectionName Collection name
     * @param array       $pipeline       Aggregation pipeline
     * @param array       $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, ?string $collectionName, array $pipeline, array $options = [])
    {
        if (! is_pipeline($pipeline, true /* allowEmpty */)) {
            throw new InvalidArgumentException('$pipeline is not a valid aggregation pipeline');
        }

        if (isset($options['allowDiskUse']) && ! is_bool($options['allowDiskUse'])) {
            throw InvalidArgumentException::invalidType('"allowDiskUse" option', $options['allowDiskUse'], 'boolean');
        }

        if (isset($options['batchSize']) && ! is_integer($options['batchSize'])) {
            throw InvalidArgumentException::invalidType('"batchSize" option', $options['batchSize'], 'integer');
        }

        if (isset($options['bypassDocumentValidation']) && ! is_bool($options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($options['codec']) && ! $options['codec'] instanceof DocumentCodec) {
            throw InvalidArgumentException::invalidType('"codec" option', $options['codec'], DocumentCodec::class);
        }

        if (isset($options['collation']) && ! is_document($options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $options['collation']);
        }

        if (isset($options['explain']) && ! is_bool($options['explain'])) {
            throw InvalidArgumentException::invalidType('"explain" option', $options['explain'], 'boolean');
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_array($options['hint']) && ! is_object($options['hint'])) {
            throw InvalidArgumentException::invalidType('"hint" option', $options['hint'], 'string or array or object');
        }

        if (isset($options['let']) && ! is_document($options['let'])) {
            throw InvalidArgumentException::expectedDocumentType('"let" option', $options['let']);
        }

        if (isset($options['maxAwaitTimeMS']) && ! is_integer($options['maxAwaitTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxAwaitTimeMS" option', $options['maxAwaitTimeMS'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['bypassDocumentValidation']) && ! $options['bypassDocumentValidation']) {
            unset($options['bypassDocumentValidation']);
        }

        if (isset($options['readConcern']) && $options['readConcern']->isDefault()) {
            unset($options['readConcern']);
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        if (isset($options['codec']) && isset($options['typeMap'])) {
            throw InvalidArgumentException::cannotCombineCodecAndTypeMap();
        }

        $this->isWrite = is_last_pipeline_operator_write($pipeline) && ! ($options['explain'] ?? false);

        if ($this->isWrite) {
            /* Ignore batchSize for writes, since no documents are returned and
             * a batchSize of zero could prevent the pipeline from executing. */
            unset($options['batchSize']);
        } else {
            unset($options['writeConcern']);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->pipeline = $pipeline;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return CursorInterface&Iterator
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

        $command = new Command(
            $this->createCommandDocument(),
            $this->createCommandOptions(),
        );

        $cursor = $this->executeCommand($server, $command);

        if (isset($this->options['codec'])) {
            return CodecCursor::fromCursor($cursor, $this->options['codec']);
        }

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return $cursor;
    }

    /**
     * Returns the command document for this operation.
     *
     * @see Explainable::getCommandDocument()
     * @return array
     */
    public function getCommandDocument()
    {
        $cmd = $this->createCommandDocument();

        // Read concern can change the query plan
        if (isset($this->options['readConcern'])) {
            $cmd['readConcern'] = $this->options['readConcern'];
        }

        return $cmd;
    }

    /**
     * Create the aggregate command document.
     */
    private function createCommandDocument(): array
    {
        $cmd = [
            'aggregate' => $this->collectionName ?? 1,
            'pipeline' => $this->pipeline,
        ];

        foreach (['allowDiskUse', 'bypassDocumentValidation', 'comment', 'explain', 'maxTimeMS'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        foreach (['collation', 'let'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = (object) $this->options[$option];
            }
        }

        if (isset($this->options['hint'])) {
            $cmd['hint'] = is_array($this->options['hint']) ? (object) $this->options['hint'] : $this->options['hint'];
        }

        $cmd['cursor'] = isset($this->options['batchSize'])
            ? ['batchSize' => $this->options['batchSize']]
            : new stdClass();

        return $cmd;
    }

    private function createCommandOptions(): array
    {
        $cmdOptions = [];

        if (isset($this->options['maxAwaitTimeMS'])) {
            $cmdOptions['maxAwaitTimeMS'] = $this->options['maxAwaitTimeMS'];
        }

        return $cmdOptions;
    }

    /**
     * Execute the aggregate command using the appropriate Server method.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executecommand.php
     * @see https://php.net/manual/en/mongodb-driver-server.executereadcommand.php
     * @see https://php.net/manual/en/mongodb-driver-server.executereadwritecommand.php
     */
    private function executeCommand(Server $server, Command $command): Cursor
    {
        $options = [];

        foreach (['readConcern', 'readPreference', 'session', 'writeConcern'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        if (! $this->isWrite) {
            return $server->executeReadCommand($this->databaseName, $command, $options);
        }

        /* Server::executeReadWriteCommand() does not support a "readPreference"
         * option, so fall back to executeCommand(). This means that libmongoc
         * will not apply any client-level options (e.g. writeConcern), but that
         * should not be an issue as PHPLIB handles inheritance on its own. */
        if (isset($options['readPreference'])) {
            return $server->executeCommand($this->databaseName, $command, $options);
        }

        return $server->executeReadWriteCommand($this->databaseName, $command, $options);
    }
}
