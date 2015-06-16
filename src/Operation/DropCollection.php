<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Exception\RuntimeException;

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
        $cursor = $server->executeCommand($this->databaseName, new Command(array('drop' => $this->collectionName)));
        $result = current($cursor->toArray());

        if (empty($result['ok'])) {
            throw new RuntimeException(isset($result['errmsg']) ? $result['errmsg'] : 'Unknown error');
        }

        return $result;
    }
}
