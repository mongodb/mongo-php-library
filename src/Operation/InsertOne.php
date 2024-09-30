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
use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\InsertOneResult;

use function is_bool;
use function MongoDB\is_document;

/**
 * Operation for inserting a single document with the insert command.
 *
 * @see \MongoDB\Collection::insertOne()
 * @see https://mongodb.com/docs/manual/reference/command/insert/
 */
final class InsertOne
{
    private array|object $document;

    /**
     * Constructs an insert command.
     *
     * Supported options:
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation.
     *
     *  * codec (MongoDB\Codec\DocumentCodec): Codec used to encode PHP objects
     *    into BSON.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $document       Document to insert
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, array|object $document, private array $options = [])
    {
        if (isset($this->options['bypassDocumentValidation']) && ! is_bool($this->options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $this->options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($this->options['codec']) && ! $this->options['codec'] instanceof DocumentCodec) {
            throw InvalidArgumentException::invalidType('"codec" option', $this->options['codec'], DocumentCodec::class);
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['writeConcern']) && ! $this->options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $this->options['writeConcern'], WriteConcern::class);
        }

        if (isset($this->options['bypassDocumentValidation']) && ! $this->options['bypassDocumentValidation']) {
            unset($this->options['bypassDocumentValidation']);
        }

        if (isset($this->options['writeConcern']) && $this->options['writeConcern']->isDefault()) {
            unset($this->options['writeConcern']);
        }

        $this->document = $this->validateDocument($document, $this->options['codec'] ?? null);
    }

    /**
     * Execute the operation.
     *
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): InsertOneResult
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if (isset($this->options['writeConcern']) && $inTransaction) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $bulk = new Bulk($this->createBulkWriteOptions());

        $insertedId = $bulk->insert($this->document);

        $writeResult = $server->executeBulkWrite($this->databaseName . '.' . $this->collectionName, $bulk, $this->createExecuteOptions());

        return new InsertOneResult($writeResult, $insertedId);
    }

    /**
     * Create options for constructing the bulk write.
     *
     * @see https://php.net/manual/en/mongodb-driver-bulkwrite.construct.php
     */
    private function createBulkWriteOptions(): array
    {
        $options = [];

        foreach (['bypassDocumentValidation', 'comment'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        return $options;
    }

    /**
     * Create options for executing the bulk write.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executebulkwrite.php
     */
    private function createExecuteOptions(): array
    {
        $options = [];

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        if (isset($this->options['writeConcern'])) {
            $options['writeConcern'] = $this->options['writeConcern'];
        }

        return $options;
    }

    private function validateDocument(array|object $document, ?DocumentCodec $codec): array|object
    {
        if ($codec) {
            $document = $codec->encode($document);
        }

        if (! is_document($document)) {
            throw InvalidArgumentException::expectedDocumentType('$document', $document);
        }

        return $document;
    }
}
