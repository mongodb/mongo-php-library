<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;

/**
 * Operation for the dropDatabase command.
 *
 * @api
 * @see MongoDB\Client::dropDatabase()
 * @see MongoDB\Database::drop()
 * @see http://docs.mongodb.org/manual/reference/command/dropDatabase/
 */
class DropDatabase implements Executable
{
    private $databaseName;

    /**
     * Constructs a dropDatabase command.
     *
     * @param string $databaseName Database name
     */
    public function __construct($databaseName)
    {
        $this->databaseName = (string) $databaseName;
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
        $cursor = $server->executeCommand($this->databaseName, new Command(['dropDatabase' => 1]));

        return current($cursor->toArray());
    }
}
