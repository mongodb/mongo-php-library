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

namespace MongoDB;

use Exception;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Operation\ListCollections;
use MongoDB\Operation\WithTransaction;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use stdClass;

use function array_is_list;
use function array_key_first;
use function assert;
use function end;
use function get_object_vars;
use function is_array;
use function is_object;
use function is_string;
use function str_ends_with;
use function substr;

/**
 * Registers a PSR-3 logger to receive log messages from the driver/library.
 *
 * Calling this method again with a logger that has already been added will have
 * no effect.
 */
function add_logger(LoggerInterface $logger): void
{
    PsrLogAdapter::addLogger($logger);
}

/**
 * Unregisters a PSR-3 logger.
 *
 * Calling this method with a logger that has not been added will have no
 * effect.
 */
function remove_logger(LoggerInterface $logger): void
{
    PsrLogAdapter::removeLogger($logger);
}

/**
 * Create a new stdClass instance with the provided properties.
 * Use named arguments to specify the property names.
 *     object( property1: value1, property2: value2 )
 *
 * If property names contain a dot or a dollar characters, use array unpacking syntax.
 *     object( ...[ 'author.name' => 1, 'array.$' => 1 ] )
 *
 * @psalm-suppress MoreSpecificReturnType
 * @psalm-suppress LessSpecificReturnStatement
 */
function object(mixed ...$values): stdClass
{
    return (object) $values;
}

/**
 * Check whether all servers support executing a write stage on a secondary.
 *
 * @internal
 * @param Server[] $servers
 */
function all_servers_support_write_stage_on_secondary(array $servers): bool
{
    /* Write stages on secondaries are technically supported by FCV 4.4, but the
     * CRUD spec requires all 5.0+ servers since FCV is not tracked by SDAM. */
    static $wireVersionForWriteStageOnSecondary = 13;

    foreach ($servers as $server) {
        // We can assume that load balancers only front 5.0+ servers
        if ($server->getType() === Server::TYPE_LOAD_BALANCER) {
            continue;
        }

        if (! server_supports_feature($server, $wireVersionForWriteStageOnSecondary)) {
            return false;
        }
    }

    return true;
}

/**
 * Applies a type map to a document.
 *
 * This function is used by operations where it is not possible to apply a type
 * map to the cursor directly because the root document is a command response
 * (e.g. findAndModify).
 *
 * @internal
 * @param array|object $document Document to which the type map will be applied
 * @param array        $typeMap  Type map for BSON deserialization.
 * @throws InvalidArgumentException
 */
function apply_type_map_to_document(array|object $document, array $typeMap): array|object
{
    if (! is_document($document)) {
        throw InvalidArgumentException::expectedDocumentType('$document', $document);
    }

    return Document::fromPHP($document)->toPHP($typeMap);
}

/**
 * Converts a document parameter to an array.
 *
 * This is used to facilitate unified access to document fields. It also handles
 * Document, PackedArray, and Serializable objects.
 *
 * This function is not used for type checking. Therefore, it does not reject
 * PackedArray objects or Serializable::bsonSerialize() return values that would
 * encode as BSON arrays.
 *
 * @internal
 * @throws InvalidArgumentException if $document is not an array or object
 */
function document_to_array(array|object $document): array
{
    if ($document instanceof Document || $document instanceof PackedArray) {
        /* Nested documents and arrays are intentionally left as BSON. We avoid
         * iterator_to_array() since Document and PackedArray iteration returns
         * all values as MongoDB\BSON\Value instances. */

        /** @psalm-var array */
        return $document->toPHP([
            'array' => 'bson',
            'document' => 'bson',
            'root' => 'array',
        ]);
    } elseif ($document instanceof Serializable) {
        $document = $document->bsonSerialize();
    }

    if (is_object($document)) {
        /* Note: this omits all uninitialized properties, whereas BSON encoding
         * includes untyped, uninitialized properties. This is acceptable given
         * document_to_array()'s use cases. */
        $document = get_object_vars($document);
    }

    return $document;
}

/**
 * Return a collection's encryptedFields from the encryptedFieldsMap
 * autoEncryption driver option (if available).
 *
 * @internal
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#collection-encryptedfields-lookup-getencryptedfields
 * @see Collection::drop()
 * @see Database::createCollection()
 * @see Database::dropCollection()
 */
function get_encrypted_fields_from_driver(string $databaseName, string $collectionName, Manager $manager): array|object|null
{
    $encryptedFieldsMap = (array) $manager->getEncryptedFieldsMap();

    return $encryptedFieldsMap[$databaseName . '.' . $collectionName] ?? null;
}

/**
 * Return a collection's encryptedFields option from the server (if any).
 *
 * @internal
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#collection-encryptedfields-lookup-getencryptedfields
 * @see Collection::drop()
 * @see Database::dropCollection()
 */
function get_encrypted_fields_from_server(string $databaseName, string $collectionName, Manager $manager, Server $server): array|object|null
{
    // No-op if the encryptedFieldsMap autoEncryption driver option was omitted
    if ($manager->getEncryptedFieldsMap() === null) {
        return null;
    }

    $collectionInfoIterator = (new ListCollections($databaseName, ['filter' => ['name' => $collectionName]]))->execute($server);

    foreach ($collectionInfoIterator as $collectionInfo) {
        /* Note: ListCollections applies a typeMap that converts BSON documents
         * to PHP arrays. This should not be problematic as encryptedFields here
         * is only used by drop helpers to obtain names of supporting encryption
         * collections. */
        return $collectionInfo['options']['encryptedFields'] ?? null;
    }

    return null;
}

/**
 * Returns whether a given value is a valid document.
 *
 * This method returns true for any array or object, but specifically excludes
 * BSON PackedArray instances
 *
 * @internal
 */
function is_document(mixed $document): bool
{
    return is_array($document) || (is_object($document) && ! $document instanceof PackedArray);
}

/**
 * Return whether the first key in the document starts with a "$" character.
 *
 * This is used for validating aggregation pipeline stages and differentiating
 * update and replacement documents. Since true and false return values may be
 * expected in different contexts, this function intentionally throws if
 * $document has an unexpected type instead of returning false.
 *
 * @internal
 * @throws InvalidArgumentException if $document is not an array or object
 */
function is_first_key_operator(array|object $document): bool
{
    if ($document instanceof PackedArray) {
        return false;
    }

    $document = document_to_array($document);

    $firstKey = array_key_first($document);

    if (! is_string($firstKey)) {
        return false;
    }

    return '$' === ($firstKey[0] ?? null);
}

/**
 * Returns whether the argument is a valid aggregation or update pipeline.
 *
 * This is primarily used for validating arguments for update and replace
 * operations, but can also be used for validating an aggregation pipeline.
 *
 * The $allowEmpty parameter can be used to control whether an empty array
 * should be considered a valid pipeline. Empty arrays are generally valid for
 * an aggregation pipeline, but the things are more complicated for update
 * pipelines.
 *
 * Update operations must prohibit empty pipelines, since libmongoc may encode
 * an empty pipeline array as an empty replacement document when writing an
 * update command (arrays and documents have the same bson_t representation).
 * For consistency, findOneAndUpdate should also prohibit empty pipelines.
 * Replace operations (e.g. replaceOne, findOneAndReplace) should reject empty
 * and non-empty pipelines alike, since neither is a replacement document.
 *
 * Note: this method may propagate an InvalidArgumentException from
 * document_or_array() if a Serializable object within the pipeline array
 * returns a non-array, non-object value from its bsonSerialize() method.
 *
 * @internal
 * @throws InvalidArgumentException
 */
function is_pipeline(array|object $pipeline, bool $allowEmpty = false): bool
{
    if ($pipeline instanceof PackedArray) {
        /* Nested documents and arrays are intentionally left as BSON. We avoid
         * iterator_to_array() since PackedArray iteration returns all values as
         * MongoDB\BSON\Value instances. */
        /** @psalm-var array */
        $pipeline = $pipeline->toPHP([
            'array' => 'bson',
            'document' => 'bson',
            'root' => 'array',
        ]);
    } elseif ($pipeline instanceof Serializable) {
        $pipeline = $pipeline->bsonSerialize();
    }

    if (! is_array($pipeline)) {
        return false;
    }

    if ($pipeline === []) {
        return $allowEmpty;
    }

    if (! array_is_list($pipeline)) {
        return false;
    }

    foreach ($pipeline as $stage) {
        if (! is_document($stage)) {
            return false;
        }

        if (! is_first_key_operator($stage)) {
            return false;
        }
    }

    return true;
}

/**
 * Returns whether the argument is a list that contains at least one
 * {@see StageInterface} object.
 *
 * @internal
 */
function is_builder_pipeline(array $pipeline): bool
{
    if (! $pipeline || ! array_is_list($pipeline)) {
        return false;
    }

    foreach ($pipeline as $stage) {
        if (is_object($stage) && $stage instanceof StageInterface) {
            return true;
        }
    }

    return false;
}

/**
 * Returns whether we are currently in a transaction.
 *
 * @internal
 * @param array $options Command options
 */
function is_in_transaction(array $options): bool
{
    if (isset($options['session']) && $options['session'] instanceof Session && $options['session']->isInTransaction()) {
        return true;
    }

    return false;
}

/**
 * Return whether the aggregation pipeline ends with an $out or $merge operator.
 *
 * This is used for determining whether the aggregation pipeline must be
 * executed against a primary server.
 *
 * @internal
 * @param array $pipeline Aggregation pipeline
 */
function is_last_pipeline_operator_write(array $pipeline): bool
{
    $lastOp = end($pipeline);

    if ($lastOp === false) {
        return false;
    }

    if (! is_array($lastOp) && ! is_object($lastOp)) {
        return false;
    }

    $key = array_key_first(document_to_array($lastOp));

    return $key === '$merge' || $key === '$out';
}

/**
 * Return whether the write concern is acknowledged.
 *
 * This function is similar to mongoc_write_concern_is_acknowledged but does not
 * check the fsync option since that was never supported in the PHP driver.
 *
 * @internal
 * @see https://mongodb.com/docs/manual/reference/write-concern/
 */
function is_write_concern_acknowledged(WriteConcern $writeConcern): bool
{
    /* Note: -1 corresponds to MONGOC_WRITE_CONCERN_W_ERRORS_IGNORED, which is
     * deprecated synonym of MONGOC_WRITE_CONCERN_W_UNACKNOWLEDGED and slated
     * for removal in libmongoc 2.0. */
    return ($writeConcern->getW() !== 0 && $writeConcern->getW() !== -1) || $writeConcern->getJournal() === true;
}

/**
 * Return whether the server supports a particular feature.
 *
 * @internal
 * @param Server  $server  Server to check
 * @param integer $feature Feature constant (i.e. wire protocol version)
 */
function server_supports_feature(Server $server, int $feature): bool
{
    $info = $server->getInfo();
    $maxWireVersion = isset($info['maxWireVersion']) ? (integer) $info['maxWireVersion'] : 0;
    $minWireVersion = isset($info['minWireVersion']) ? (integer) $info['minWireVersion'] : 0;

    return $minWireVersion <= $feature && $maxWireVersion >= $feature;
}

/**
 * Return whether the input is an array of strings.
 *
 * @internal
 */
function is_string_array(mixed $input): bool
{
    if (! is_array($input)) {
        return false;
    }

    foreach ($input as $item) {
        if (! is_string($item)) {
            return false;
        }
    }

    return true;
}

/**
 * Performs a deep copy of a value.
 *
 * This function will clone objects and recursively copy values within arrays.
 *
 * @internal
 * @see https://bugs.php.net/bug.php?id=49664
 * @param mixed $element Value to be copied
 * @throws ReflectionException
 */
function recursive_copy(mixed $element): mixed
{
    if (is_array($element)) {
        foreach ($element as $key => $value) {
            $element[$key] = recursive_copy($value);
        }

        return $element;
    }

    if (! is_object($element)) {
        return $element;
    }

    if (! (new ReflectionClass($element))->isCloneable()) {
        return $element;
    }

    return clone $element;
}

/**
 * Creates a type map to apply to a field type
 *
 * This is used in the Aggregate, Distinct, and FindAndModify operations to
 * apply the root-level type map to the document that will be returned. It also
 * replaces the root type with object for consistency within these operations
 *
 * An existing type map for the given field path will not be overwritten
 *
 * @internal
 * @param array  $typeMap   The existing typeMap
 * @param string $fieldPath The field path to apply the root type to
 */
function create_field_path_type_map(array $typeMap, string $fieldPath): array
{
    // If some field paths already exist, we prefix them with the field path we are assuming as the new root
    if (isset($typeMap['fieldPaths']) && is_array($typeMap['fieldPaths'])) {
        $fieldPaths = $typeMap['fieldPaths'];

        $typeMap['fieldPaths'] = [];
        foreach ($fieldPaths as $existingFieldPath => $type) {
            $typeMap['fieldPaths'][$fieldPath . '.' . $existingFieldPath] = $type;
        }
    }

    // If a root typemap was set, apply this to the field object
    if (isset($typeMap['root'])) {
        $typeMap['fieldPaths'][$fieldPath] = $typeMap['root'];
    }

    /* Special case if we want to convert an array, in which case we need to
     * ensure that the field containing the array is exposed as an array,
     * instead of the type given in the type map's array key. */
    if (str_ends_with($fieldPath, '.$')) {
        $typeMap['fieldPaths'][substr($fieldPath, 0, -2)] = 'array';
    }

    $typeMap['root'] = 'object';

    return $typeMap;
}

/**
 * Execute a callback within a transaction in the given session
 *
 * This helper takes care of retrying the commit operation or the entire
 * transaction if an error occurs.
 *
 * If the commit fails because of an UnknownTransactionCommitResult error, the
 * commit is retried without re-invoking the callback.
 * If the commit fails because of a TransientTransactionError, the entire
 * transaction will be retried. In this case, the callback will be invoked
 * again. It is important that the logic inside the callback is idempotent.
 *
 * In case of failures, the commit or transaction are retried until 120 seconds
 * from the initial call have elapsed. After that, no retries will happen and
 * the helper will throw the last exception received from the driver.
 *
 * @see Client::startSession()
 * @see Session::startTransaction() for supported transaction options
 *
 * @param Session  $session            A session object as retrieved by Client::startSession
 * @param callable $callback           A callback that will be invoked within the transaction
 * @param array    $transactionOptions Additional options that are passed to Session::startTransaction
 * @throws RuntimeException for driver errors while committing the transaction
 * @throws Exception for any other errors, including those thrown in the callback
 */
function with_transaction(Session $session, callable $callback, array $transactionOptions = []): void
{
    $operation = new WithTransaction($callback, $transactionOptions);
    $operation->execute($session);
}

/**
 * Returns the session option if it is set and valid.
 *
 * @internal
 */
function extract_session_from_options(array $options): ?Session
{
    if (isset($options['session']) && $options['session'] instanceof Session) {
        return $options['session'];
    }

    return null;
}

/**
 * Returns the readPreference option if it is set and valid.
 *
 * @internal
 */
function extract_read_preference_from_options(array $options): ?ReadPreference
{
    if (isset($options['readPreference']) && $options['readPreference'] instanceof ReadPreference) {
        return $options['readPreference'];
    }

    return null;
}

/**
 * Performs server selection, respecting the readPreference and session options.
 *
 * The pinned server for an active transaction takes priority, followed by an
 * operation-level read preference, followed by an active transaction's read
 * preference, followed by a primary read preference.
 *
 * @internal
 */
function select_server(Manager $manager, array $options): Server
{
    $session = extract_session_from_options($options);
    $server = $session instanceof Session ? $session->getServer() : null;

    // Pinned server for an active transaction takes priority
    if ($server !== null) {
        return $server;
    }

    // Operation read preference takes priority
    $readPreference = extract_read_preference_from_options($options);

    // Read preference for an active transaction takes priority
    if ($readPreference === null && $session instanceof Session && $session->isInTransaction()) {
        /* Session::getTransactionOptions() should always return an array if the
         * session is in a transaction, but we can be defensive. */
        $readPreference = extract_read_preference_from_options($session->getTransactionOptions() ?? []);
    }

    // Manager::selectServer() defaults to a primary read preference
    return $manager->selectServer($readPreference);
}

/**
 * Performs server selection for an aggregate operation with a write stage. The
 * $options parameter may be modified by reference if a primary read preference
 * must be forced due to the existence of pre-5.0 servers in the topology.
 *
 * @internal
 * @see https://github.com/mongodb/specifications/blob/master/source/crud/crud.rst#aggregation-pipelines-with-write-stages
 */
function select_server_for_aggregate_write_stage(Manager $manager, array &$options): Server
{
    $readPreference = extract_read_preference_from_options($options);

    /* If there is either no read preference or a primary read preference, there
     * is no special server selection logic to apply.
     *
     * Note: an alternative read preference could still be inherited from an
     * active transaction's options, but we can rely on libmongoc to raise a
     * "read preference in a transaction must be primary" error if necessary. */
    if ($readPreference === null || $readPreference->getModeString() === ReadPreference::PRIMARY) {
        return select_server($manager, $options);
    }

    $server = null;
    $serverSelectionError = null;

    try {
        $server = select_server($manager, $options);
    } catch (DriverRuntimeException $serverSelectionError) {
    }

    /* If any pre-5.0 servers exist in the topology, force a primary read
     * preference and repeat server selection if it previously failed or
     * selected a secondary. */
    if (! all_servers_support_write_stage_on_secondary($manager->getServers())) {
        $options['readPreference'] = new ReadPreference(ReadPreference::PRIMARY);

        if ($server === null || $server->isSecondary()) {
            return select_server($manager, $options);
        }
    }

    /* If the topology only contains 5.0+ servers, we should either return the
     * previously selected server or propagate the server selection error. */
    if ($serverSelectionError !== null) {
        throw $serverSelectionError;
    }

    assert($server instanceof Server);

    return $server;
}

/**
 * Performs server selection for a write operation.
 *
 * The pinned server for an active transaction takes priority, followed by an
 * operation-level read preference, followed by a primary read preference. This
 * is similar to select_server() except that it ignores a read preference from
 * an active transaction's options.
 *
 * @internal
 */
function select_server_for_write(Manager $manager, array $options): Server
{
    return select_server($manager, $options + ['readPreference' => new ReadPreference(ReadPreference::PRIMARY)]);
}
