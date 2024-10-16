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
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;

use function current;
use function is_array;
use function is_bool;
use function is_integer;
use function is_string;
use function MongoDB\is_document;
use function MongoDB\is_pipeline;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Operation for the create command.
 *
 * @see \MongoDB\Database::createCollection()
 * @see https://mongodb.com/docs/manual/reference/command/create/
 *
 * @final extending this class will not be supported in v2.0.0
 */
class CreateCollection implements Executable
{
    /** @deprecated 1.21 */
    public const USE_POWER_OF_2_SIZES = 1;

    /** @deprecated 1.21 */
    public const NO_PADDING = 2;

    /**
     * Constructs a create command.
     *
     * Supported options:
     *
     *  * autoIndexId (boolean): Specify false to disable the automatic creation
     *    of an index on the _id field. For replica sets, this option cannot be
     *    false. The default is true.
     *
     *    This option has been deprecated since MongoDB 3.2. As of MongoDB 4.0,
     *    this option cannot be false when creating a replicated collection
     *    (i.e. a collection outside of the local database in any mongod mode).
     *
     *  * capped (boolean): Specify true to create a capped collection. If set,
     *    the size option must also be specified. The default is false.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * changeStreamPreAndPostImages (document): Used to configure support for
     *    pre- and post-images in change streams.
     *
     *    This is not supported for server versions < 6.0.
     *
     *  * clusteredIndex (document): A clustered index specification.
     *
     *    This is not supported for server versions < 5.3.
     *
     *  * collation (document): Collation specification.
     *
     *  * encryptedFields (document): Configuration for encrypted fields.
     *    See: https://www.mongodb.com/docs/manual/core/queryable-encryption/fundamentals/encrypt-and-query/
     *
     *  * expireAfterSeconds: The TTL for documents in time series collections.
     *
     *    This is not supported for servers versions < 5.0.
     *
     *  * flags (integer): Options for the MMAPv1 storage engine only. Must be a
     *    bitwise combination CreateCollection::USE_POWER_OF_2_SIZES and
     *    CreateCollection::NO_PADDING. The default is
     *    CreateCollection::USE_POWER_OF_2_SIZES.
     *
     *  * indexOptionDefaults (document): Default configuration for indexes when
     *    creating the collection.
     *
     *  * max (integer): The maximum number of documents allowed in the capped
     *    collection. The size option takes precedence over this limit.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * pipeline (array): An array that consists of the aggregation pipeline
     *    stage(s), which will be applied to the collection or view specified by
     *    viewOn.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * size (integer): The maximum number of bytes for a capped collection.
     *
     *  * storageEngine (document): Storage engine options.
     *
     *  * timeseries (document): Options for time series collections.
     *
     *    This is not supported for servers versions < 5.0.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will only be
     *    used for the returned command result document.
     *
     *  * validationAction (string): Validation action.
     *
     *  * validationLevel (string): Validation level.
     *
     *  * validator (document): Validation rules or expressions.
     *
     *  * viewOn (string): The name of the source collection or view from which
     *    to create the view.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @see https://source.wiredtiger.com/2.4.1/struct_w_t___s_e_s_s_i_o_n.html#a358ca4141d59c345f401c58501276bbb
     * @see https://mongodb.com/docs/manual/core/schema-validation/
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private string $databaseName, private string $collectionName, private array $options = [])
    {
        if (isset($this->options['autoIndexId']) && ! is_bool($this->options['autoIndexId'])) {
            throw InvalidArgumentException::invalidType('"autoIndexId" option', $this->options['autoIndexId'], 'boolean');
        }

        if (isset($this->options['capped']) && ! is_bool($this->options['capped'])) {
            throw InvalidArgumentException::invalidType('"capped" option', $this->options['capped'], 'boolean');
        }

        if (isset($this->options['changeStreamPreAndPostImages']) && ! is_document($this->options['changeStreamPreAndPostImages'])) {
            throw InvalidArgumentException::expectedDocumentType('"changeStreamPreAndPostImages" option', $this->options['changeStreamPreAndPostImages']);
        }

        if (isset($this->options['clusteredIndex']) && ! is_document($this->options['clusteredIndex'])) {
            throw InvalidArgumentException::expectedDocumentType('"clusteredIndex" option', $this->options['clusteredIndex']);
        }

        if (isset($this->options['collation']) && ! is_document($this->options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $this->options['collation']);
        }

        if (isset($this->options['encryptedFields']) && ! is_document($this->options['encryptedFields'])) {
            throw InvalidArgumentException::expectedDocumentType('"encryptedFields" option', $this->options['encryptedFields']);
        }

        if (isset($this->options['expireAfterSeconds']) && ! is_integer($this->options['expireAfterSeconds'])) {
            throw InvalidArgumentException::invalidType('"expireAfterSeconds" option', $this->options['expireAfterSeconds'], 'integer');
        }

        if (isset($this->options['flags']) && ! is_integer($this->options['flags'])) {
            throw InvalidArgumentException::invalidType('"flags" option', $this->options['flags'], 'integer');
        }

        if (isset($this->options['indexOptionDefaults']) && ! is_document($this->options['indexOptionDefaults'])) {
            throw InvalidArgumentException::expectedDocumentType('"indexOptionDefaults" option', $this->options['indexOptionDefaults']);
        }

        if (isset($this->options['max']) && ! is_integer($this->options['max'])) {
            throw InvalidArgumentException::invalidType('"max" option', $this->options['max'], 'integer');
        }

        if (isset($this->options['maxTimeMS']) && ! is_integer($this->options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $this->options['maxTimeMS'], 'integer');
        }

        if (isset($this->options['pipeline']) && ! is_array($this->options['pipeline'])) {
            throw InvalidArgumentException::invalidType('"pipeline" option', $this->options['pipeline'], 'array');
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['size']) && ! is_integer($this->options['size'])) {
            throw InvalidArgumentException::invalidType('"size" option', $this->options['size'], 'integer');
        }

        if (isset($this->options['storageEngine']) && ! is_document($this->options['storageEngine'])) {
            throw InvalidArgumentException::expectedDocumentType('"storageEngine" option', $this->options['storageEngine']);
        }

        if (isset($this->options['timeseries']) && ! is_document($this->options['timeseries'])) {
            throw InvalidArgumentException::expectedDocumentType('"timeseries" option', $this->options['timeseries']);
        }

        if (isset($this->options['typeMap']) && ! is_array($this->options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $this->options['typeMap'], 'array');
        }

        if (isset($this->options['validationAction']) && ! is_string($this->options['validationAction'])) {
            throw InvalidArgumentException::invalidType('"validationAction" option', $this->options['validationAction'], 'string');
        }

        if (isset($this->options['validationLevel']) && ! is_string($this->options['validationLevel'])) {
            throw InvalidArgumentException::invalidType('"validationLevel" option', $this->options['validationLevel'], 'string');
        }

        if (isset($this->options['validator']) && ! is_document($this->options['validator'])) {
            throw InvalidArgumentException::expectedDocumentType('"validator" option', $this->options['validator']);
        }

        if (isset($this->options['viewOn']) && ! is_string($this->options['viewOn'])) {
            throw InvalidArgumentException::invalidType('"viewOn" option', $this->options['viewOn'], 'string');
        }

        if (isset($this->options['writeConcern']) && ! $this->options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $this->options['writeConcern'], WriteConcern::class);
        }

        if (isset($this->options['writeConcern']) && $this->options['writeConcern']->isDefault()) {
            unset($this->options['writeConcern']);
        }

        if (isset($this->options['autoIndexId'])) {
            trigger_error('The "autoIndexId" option is deprecated and will be removed in version 2.0', E_USER_DEPRECATED);
        }

        if (isset($this->options['flags'])) {
            trigger_error('The "flags" option is deprecated and will be removed in version 2.0', E_USER_DEPRECATED);
        }

        if (isset($this->options['pipeline']) && ! is_pipeline($this->options['pipeline'], true /* allowEmpty */)) {
            throw new InvalidArgumentException('"pipeline" option is not a valid aggregation pipeline');
        }
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return array|object Command result document
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $cursor = $server->executeWriteCommand($this->databaseName, $this->createCommand(), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create the create command.
     */
    private function createCommand(): Command
    {
        $cmd = ['create' => $this->collectionName];

        foreach (['autoIndexId', 'capped', 'comment', 'expireAfterSeconds', 'flags', 'max', 'maxTimeMS', 'pipeline', 'size', 'validationAction', 'validationLevel', 'viewOn'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        foreach (['changeStreamPreAndPostImages', 'clusteredIndex', 'collation', 'encryptedFields', 'indexOptionDefaults', 'storageEngine', 'timeseries', 'validator'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = (object) $this->options[$option];
            }
        }

        return new Command($cmd);
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executewritecommand.php
     */
    private function createOptions(): array
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
}
