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
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\UpdateResult;

use function MongoDB\is_document;
use function MongoDB\is_first_key_operator;
use function MongoDB\is_pipeline;

/**
 * Operation for replacing a single document with the update command.
 *
 * @see \MongoDB\Collection::replaceOne()
 * @see https://mongodb.com/docs/manual/reference/command/update/
 *
 * @final extending this class will not be supported in v2.0.0
 */
class ReplaceOne implements Executable
{
    private Update $update;

    /**
     * Constructs an update command.
     *
     * Supported options:
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation.
     *
     *  * codec (MongoDB\Codec\DocumentCodec): Codec used to encode PHP objects
     *    into BSON.
     *
     *  * collation (document): Collation specification.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *    This is not supported for server versions < 4.2 and will result in an
     *    exception at execution time if used.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * upsert (boolean): When true, a new document is created if no document
     *    matches the query. The default is false.
     *
     *  * let (document): Map of parameter names and values. Values must be
     *    constant or closed expressions that do not reference document fields.
     *    Parameters can then be accessed as variables in an aggregate
     *    expression context (e.g. "$$var").
     *
     *   * sort (document): Determines which document the operation modifies if
     *     the query selects multiple documents.
     *
     *     This is not supported for server versions < 8.0 and will result in an
     *     exception at execution time if used.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to filter documents
     * @param array|object $replacement    Replacement document
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, array|object $filter, array|object $replacement, array $options = [])
    {
        if (isset($options['codec']) && ! $options['codec'] instanceof DocumentCodec) {
            throw InvalidArgumentException::invalidType('"codec" option', $options['codec'], DocumentCodec::class);
        }

        if (isset($options['codec'], $options['typeMap'])) {
            throw InvalidArgumentException::cannotCombineCodecAndTypeMap();
        }

        $this->update = new Update(
            $databaseName,
            $collectionName,
            $filter,
            $this->validateReplacement($replacement, $options['codec'] ?? null),
            ['multi' => false] + $options,
        );
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return UpdateResult
     * @throws UnsupportedException if collation is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        return $this->update->execute($server);
    }

    /** @return array|object */
    private function validateReplacement(array|object $replacement, ?DocumentCodec $codec)
    {
        if ($codec) {
            $replacement = $codec->encode($replacement);
        }

        if (! is_document($replacement)) {
            throw InvalidArgumentException::expectedDocumentType('$replacement', $replacement);
        }

        // Treat empty arrays as replacement documents for BC
        if ($replacement === []) {
            $replacement = (object) $replacement;
        }

        if (is_first_key_operator($replacement)) {
            throw new InvalidArgumentException('First key in $replacement is an update operator');
        }

        if (is_pipeline($replacement, true /* allowEmpty */)) {
            throw new InvalidArgumentException('$replacement is an update pipeline');
        }

        return $replacement;
    }
}
