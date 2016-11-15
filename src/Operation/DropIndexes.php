<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for the dropIndexes command.
 *
 * @api
 * @see \MongoDB\Collection::dropIndexes()
 * @see http://docs.mongodb.org/manual/reference/command/dropIndexes/
 */
class DropIndexes implements Executable
{
    private static $wireVersionForWriteConcern = 5;

    private $databaseName;
    private $collectionName;
    private $indexName;
    private $options;

    /**
     * Constructs a dropIndexes command.
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
     * @param string $indexName      Index name (use "*" to drop all indexes)
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, $indexName, array $options = [])
    {
        $indexName = (string) $indexName;

        if ($indexName === '') {
            throw new InvalidArgumentException('$indexName cannot be empty');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->indexName = $indexName;
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

        $cursor = $server->executeCommand($this->databaseName, $this->createCommand());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create the dropIndexes command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = [
            'dropIndexes' => $this->collectionName,
            'index' => $this->indexName,
        ];

        if (isset($this->options['writeConcern'])) {
            $cmd['writeConcern'] = \MongoDB\write_concern_as_document($this->options['writeConcern']);
        }

        return new Command($cmd);
    }
}
