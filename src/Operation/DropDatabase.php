<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Exception\RuntimeException;

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
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
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
        $cursor = $server->executeCommand($this->databaseName, new Command(array('dropDatabase' => 1)));
        $cursor->setTypeMap(array('document' => 'stdClass'));
        $result = current($cursor->toArray());

        // TODO: Remove this once PHPC-318 is implemented
        is_array($result) and $result = (object) $result;

        if (empty($result->ok)) {
            throw new RuntimeException(isset($result->errmsg) ? $result->errmsg : 'Unknown error');
        }

        return $result;
    }
}
