<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for the drop command.
 *
 * @api
 * @see \MongoDB\Collection::drop()
 * @see \MongoDB\Database::dropCollection()
 * @see http://docs.mongodb.org/manual/reference/command/drop/
 */
class DropCollection implements Executable
{
    private static $errorMessageNamespaceNotFound = 'ns not found';
    private static $wireVersionForWriteConcern = 5;

    private $databaseName;
    private $collectionName;
    private $options;

    /**
     * Constructs a drop command.
     *
     * Supported options:
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be used
     *    for the returned command result document.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, array $options = [])
    {
        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return array|object Command result document
     * @throws UnsupportedException if writeConcern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        if (isset($this->options['writeConcern']) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForWriteConcern)) {
            throw UnsupportedException::writeConcernNotSupported();
        }

        try {
            $cursor = $server->executeCommand($this->databaseName, $this->createCommand());
        } catch (DriverRuntimeException $e) {
            /* The server may return an error if the collection does not exist.
             * Check for an error message (unfortunately, there isn't a code)
             * and NOP instead of throwing.
             */
            if ($e->getMessage() === self::$errorMessageNamespaceNotFound) {
                return (object) ['ok' => 0, 'errmsg' => self::$errorMessageNamespaceNotFound];
            }

            throw $e;
        }

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create the drop command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = ['drop' => $this->collectionName];

        if (isset($this->options['writeConcern'])) {
            $cmd['writeConcern'] = \MongoDB\write_concern_as_document($this->options['writeConcern']);
        }

        return new Command($cmd);
    }
}
