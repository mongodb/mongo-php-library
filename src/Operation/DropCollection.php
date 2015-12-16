<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Driver\Exception\RuntimeException;

/**
 * Operation for the drop command.
 *
 * @api
 * @see MongoDB\Collection::drop()
 * @see MongoDB\Database::dropCollection()
 * @see http://docs.mongodb.org/manual/reference/command/drop/
 */
class DropCollection implements Executable
{
    private static $errorMessageNamespaceNotFound = 'ns not found';
    private $databaseName;
    private $collectionName;

    /**
     * Constructs a drop command.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     */
    public function __construct($databaseName, $collectionName)
    {
        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return object Command result document
     */
    public function execute(Server $server)
    {
        try {
            $cursor = $server->executeCommand($this->databaseName, new Command(['drop' => $this->collectionName]));
        } catch (RuntimeException $e) {
            /* The server may return an error if the collection does not exist.
             * Check for an error message (unfortunately, there isn't a code)
             * and NOP instead of throwing.
             */
            if ($e->getMessage() === self::$errorMessageNamespaceNotFound) {
                return (object) ['ok' => 0, 'errmsg' => self::$errorMessageNamespaceNotFound];
            }

            throw $e;
        }

        return current($cursor->toArray());
    }
}
