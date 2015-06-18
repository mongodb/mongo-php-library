<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Query;
use MongoDB\Driver\Server;
use MongoDB\Model\IndexInfoIterator;
use MongoDB\Model\IndexInfoIteratorIterator;

/**
 * Operation for the listIndexes command.
 *
 * @api
 * @see MongoDB\Collection::listIndexes()
 * @see http://docs.mongodb.org/manual/reference/command/listIndexes/
 */
class ListIndexes implements Executable
{
    private static $wireVersionForCommand = 3;

    private $databaseName;
    private $collectionName;
    private $options;

    /**
     * Constructs a listIndexes command.
     *
     * Supported options:
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     */
    public function __construct($databaseName, $collectionName, array $options = array())
    {
        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw new InvalidArgumentTypeException('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
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
     * @return IndexInfoIterator
     */
    public function execute(Server $server)
    {
        return \MongoDB\server_supports_feature($server, self::$wireVersionForCommand)
            ? $this->executeCommand($server)
            : $this->executeLegacy($server);
    }

    /**
     * Returns information for all indexes for this collection using the
     * listIndexes command.
     *
     * @param Server $server
     * @return IndexInfoIteratorIterator
     */
    private function executeCommand(Server $server)
    {
        $cmd = array('listIndexes' => $this->collectionName);

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        $cursor = $server->executeCommand($this->databaseName, new Command($cmd));
        $cursor->setTypeMap(array('document' => 'array'));

        return new IndexInfoIteratorIterator($cursor);
    }

    /**
     * Returns information for all indexes for this collection by querying the
     * "system.indexes" collection (MongoDB <3.0).
     *
     * @param Server $server
     * @return IndexInfoIteratorIterator
     */
    private function executeLegacy(Server $server)
    {
        $filter = array('ns' => $this->databaseName . '.' . $this->collectionName);

        $options = isset($this->options['maxTimeMS'])
            ? array('modifiers' => array('$maxTimeMS' => $this->options['maxTimeMS']))
            : array();

        $cursor = $server->executeQuery($this->databaseName . '.system.indexes', new Query($filter, $options));
        $cursor->setTypeMap(array('document' => 'array'));

        return new IndexInfoIteratorIterator($cursor);
    }
}
