<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedTypeException;
use MongoDB\Model\IndexInput;

/**
 * Operation for the createIndexes command.
 *
 * @api
 * @see MongoDB\Collection::createIndex()
 * @see MongoDB\Collection::createIndexes()
 * @see http://docs.mongodb.org/manual/reference/command/createIndexes/
 */
class CreateIndexes implements Executable
{
    private static $wireVersionForCommand = 2;

    private $databaseName;
    private $collectionName;
    private $indexes = array();

    /**
     * Constructs a createIndexes command.
     *
     * @param string  $databaseName   Database name
     * @param string  $collectionName Collection name
     * @param array[] $indexes        List of index specifications
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, array $indexes)
    {
        if (empty($indexes)) {
            throw new InvalidArgumentException('$indexes is empty');
        }

        foreach ($indexes as $index) {
            if ( ! is_array($index)) {
                throw new UnexpectedTypeException($index, 'array');
            }

            if ( ! isset($index['ns'])) {
                $index['ns'] = $databaseName . '.' . $collectionName;
            }

            $this->indexes[] = new IndexInput($index);
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
    }

    /**
     * Execute the operation.
     *
     * For servers < 2.6, this will actually perform an insert operation on the
     * database's "system.indexes" collection.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return string[] The names of the created indexes
     */
    public function execute(Server $server)
    {
        if (\MongoDB\server_supports_feature($server, self::$wireVersionForCommand)) {
            $this->executeCommand($server);
        } else {
            $this->executeLegacy($server);
        }

        return array_map(function(IndexInput $index) { return (string) $index; }, $this->indexes);
    }

    /**
     * Create one or more indexes for the collection using the createIndexes
     * command.
     *
     * @param Server $server
     */
    private function executeCommand(Server $server)
    {
        $command = new Command(array(
            'createIndexes' => $this->collectionName,
            'indexes' => $this->indexes,
        ));

        $cursor = $server->executeCommand($this->databaseName, $command);
        $result = current($cursor->toArray());

        if (empty($result['ok'])) {
            throw new RuntimeException(isset($result['errmsg']) ? $result['errmsg'] : 'Unknown error');
        }
    }

    /**
     * Create one or more indexes for the collection by inserting into the
     * "system.indexes" collection (MongoDB <2.6).
     *
     * @param Server $server
     * @param IndexInput[] $indexes
     */
    private function executeLegacy(Server $server)
    {
        $bulk = new BulkWrite(true);

        foreach ($this->indexes as $index) {
            $bulk->insert($index);
        }

        $server->executeBulkWrite($this->databaseName . '.system.indexes', $bulk);
    }
}
