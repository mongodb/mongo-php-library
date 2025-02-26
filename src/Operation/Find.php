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

use MongoDB\Codec\DocumentCodec;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\CodecCursor;

use function assert;
use function is_array;
use function is_bool;
use function is_integer;
use function is_string;
use function MongoDB\is_document;

/**
 * Operation for the find command.
 *
 * @see \MongoDB\Collection::find()
 * @see https://mongodb.com/docs/manual/tutorial/query-documents/
 * @see https://mongodb.com/docs/manual/reference/operator/query-modifier/
 */
final class Find implements Explainable
{
    public const NON_TAILABLE = 1;
    public const TAILABLE = 2;
    public const TAILABLE_AWAIT = 3;

    /**
     * Constructs a find command.
     *
     * Supported options:
     *
     *  * allowDiskUse (boolean): Enables writing to temporary files. When set
     *    to true, queries can write data to the _tmp sub-directory in the
     *    dbPath directory.
     *
     *  * allowPartialResults (boolean): Get partial results from a mongos if
     *    some shards are inaccessible (instead of throwing an error).
     *
     *  * batchSize (integer): The number of documents to return per batch.
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
     *  * cursorType (enum): Indicates the type of cursor to use. Must be either
     *    NON_TAILABLE, TAILABLE, or TAILABLE_AWAIT. The default is
     *    NON_TAILABLE.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *  * limit (integer): The maximum number of documents to return.
     *
     *  * max (document): The exclusive upper bound for a specific index.
     *
     *  * maxAwaitTimeMS (integer): The maxium amount of time for the server to wait
     *    on new documents to satisfy a query, if cursorType is TAILABLE_AWAIT.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * min (document): The inclusive upper bound for a specific index.
     *
     *  * noCursorTimeout (boolean): The server normally times out idle cursors
     *    after an inactivity period (10 minutes) to prevent excess memory use.
     *    Set this option to prevent that.
     *
     *  * projection (document): Limits the fields to return for the matching
     *    document.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * returnKey (boolean): If true, returns only the index keys in the
     *    resulting documents.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * showRecordId (boolean): Determines whether to return the record
     *    identifier for each document. If true, adds a field $recordId to the
     *    returned documents.
     *
     *  * skip (integer): The number of documents to skip before returning.
     *
     *  * sort (document): The order in which to return matching documents.
     *
     *  * let (document): Map of parameter names and values. Values must be
     *    constant or closed expressions that do not reference document fields.
     *    Parameters can then be accessed as variables in an aggregate
     *    expression context (e.g. "$$var").
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, private array|object $filter, private array $options = [])
    {
        if (! is_document($filter)) {
            throw InvalidArgumentException::expectedDocumentType('$filter', $filter);
        }

        if (isset($this->options['allowDiskUse']) && ! is_bool($this->options['allowDiskUse'])) {
            throw InvalidArgumentException::invalidType('"allowDiskUse" option', $this->options['allowDiskUse'], 'boolean');
        }

        if (isset($this->options['allowPartialResults']) && ! is_bool($this->options['allowPartialResults'])) {
            throw InvalidArgumentException::invalidType('"allowPartialResults" option', $this->options['allowPartialResults'], 'boolean');
        }

        if (isset($this->options['batchSize']) && ! is_integer($this->options['batchSize'])) {
            throw InvalidArgumentException::invalidType('"batchSize" option', $this->options['batchSize'], 'integer');
        }

        if (isset($this->options['codec']) && ! $this->options['codec'] instanceof DocumentCodec) {
            throw InvalidArgumentException::invalidType('"codec" option', $this->options['codec'], DocumentCodec::class);
        }

        if (isset($this->options['collation']) && ! is_document($this->options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $this->options['collation']);
        }

        if (isset($this->options['cursorType'])) {
            if (! is_integer($this->options['cursorType'])) {
                throw InvalidArgumentException::invalidType('"cursorType" option', $this->options['cursorType'], 'integer');
            }

            if (
                $this->options['cursorType'] !== self::NON_TAILABLE &&
                $this->options['cursorType'] !== self::TAILABLE &&
                $this->options['cursorType'] !== self::TAILABLE_AWAIT
            ) {
                throw new InvalidArgumentException('Invalid value for "cursorType" option: ' . $this->options['cursorType']);
            }
        }

        if (isset($this->options['hint']) && ! is_string($this->options['hint']) && ! is_document($this->options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $this->options['hint']);
        }

        if (isset($this->options['limit']) && ! is_integer($this->options['limit'])) {
            throw InvalidArgumentException::invalidType('"limit" option', $this->options['limit'], 'integer');
        }

        if (isset($this->options['max']) && ! is_document($this->options['max'])) {
            throw InvalidArgumentException::expectedDocumentType('"max" option', $this->options['max']);
        }

        if (isset($this->options['maxAwaitTimeMS']) && ! is_integer($this->options['maxAwaitTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxAwaitTimeMS" option', $this->options['maxAwaitTimeMS'], 'integer');
        }

        if (isset($this->options['maxTimeMS']) && ! is_integer($this->options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $this->options['maxTimeMS'], 'integer');
        }

        if (isset($this->options['min']) && ! is_document($this->options['min'])) {
            throw InvalidArgumentException::expectedDocumentType('"min" option', $this->options['min']);
        }

        if (isset($this->options['noCursorTimeout']) && ! is_bool($this->options['noCursorTimeout'])) {
            throw InvalidArgumentException::invalidType('"noCursorTimeout" option', $this->options['noCursorTimeout'], 'boolean');
        }

        if (isset($this->options['projection']) && ! is_document($this->options['projection'])) {
            throw InvalidArgumentException::expectedDocumentType('"projection" option', $this->options['projection']);
        }

        if (isset($this->options['readConcern']) && ! $this->options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $this->options['readConcern'], ReadConcern::class);
        }

        if (isset($this->options['readPreference']) && ! $this->options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $this->options['readPreference'], ReadPreference::class);
        }

        if (isset($this->options['returnKey']) && ! is_bool($this->options['returnKey'])) {
            throw InvalidArgumentException::invalidType('"returnKey" option', $this->options['returnKey'], 'boolean');
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['showRecordId']) && ! is_bool($this->options['showRecordId'])) {
            throw InvalidArgumentException::invalidType('"showRecordId" option', $this->options['showRecordId'], 'boolean');
        }

        if (isset($this->options['skip']) && ! is_integer($this->options['skip'])) {
            throw InvalidArgumentException::invalidType('"skip" option', $this->options['skip'], 'integer');
        }

        if (isset($this->options['sort']) && ! is_document($this->options['sort'])) {
            throw InvalidArgumentException::expectedDocumentType('"sort" option', $this->options['sort']);
        }

        if (isset($this->options['typeMap']) && ! is_array($this->options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $this->options['typeMap'], 'array');
        }

        if (isset($this->options['let']) && ! is_document($this->options['let'])) {
            throw InvalidArgumentException::expectedDocumentType('"let" option', $this->options['let']);
        }

        if (isset($this->options['readConcern']) && $this->options['readConcern']->isDefault()) {
            unset($this->options['readConcern']);
        }

        if (isset($this->options['codec']) && isset($this->options['typeMap'])) {
            throw InvalidArgumentException::cannotCombineCodecAndTypeMap();
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnsupportedException if read concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): CursorInterface
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['readConcern'])) {
            throw UnsupportedException::readConcernNotSupportedInTransaction();
        }

        $cursor = $server->executeQuery($this->databaseName . '.' . $this->collectionName, new Query($this->filter, $this->createQueryOptions()), $this->createExecuteOptions());

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
     */
    public function getCommandDocument(): array
    {
        $cmd = ['find' => $this->collectionName, 'filter' => (object) $this->filter];

        $options = $this->createQueryOptions();

        if (empty($options)) {
            return $cmd;
        }

        // maxAwaitTimeMS is a Query level option so should not be considered here
        unset($options['maxAwaitTimeMS']);

        return $cmd + $options;
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executequery.php
     */
    private function createExecuteOptions(): array
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

    /**
     * Create options for the find query.
     *
     * Note that these are separate from the options for executing the command,
     * which are created in createExecuteOptions().
     */
    private function createQueryOptions(): array
    {
        $options = [];

        if (isset($this->options['cursorType'])) {
            if ($this->options['cursorType'] === self::TAILABLE) {
                $options['tailable'] = true;
            }

            if ($this->options['cursorType'] === self::TAILABLE_AWAIT) {
                $options['tailable'] = true;
                $options['awaitData'] = true;
            }
        }

        foreach (['allowDiskUse', 'allowPartialResults', 'batchSize', 'comment', 'hint', 'limit', 'maxAwaitTimeMS', 'maxTimeMS', 'noCursorTimeout', 'projection', 'readConcern', 'returnKey', 'showRecordId', 'skip', 'sort'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        foreach (['collation', 'let', 'max', 'min'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = (object) $this->options[$option];
            }
        }

        // Ensure no cursor is left behind when limit == batchSize by increasing batchSize
        if (isset($options['limit'], $options['batchSize']) && $options['limit'] === $options['batchSize']) {
            assert(is_integer($options['batchSize']));
            $options['batchSize']++;
        }

        if (isset($options['limit']) && $options['limit'] === 1) {
            $options['singleBatch'] = true;
        }

        return $options;
    }
}
