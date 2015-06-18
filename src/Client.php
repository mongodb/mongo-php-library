<?php

namespace MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\ListDatabases;

class Client
{
    private $manager;
    private $readPreference;
    private $writeConcern;

    /**
     * Constructs a new Client instance.
     *
     * This is the preferred class for connecting to a MongoDB server or
     * cluster of servers. It serves as a gateway for accessing individual
     * databases and collections.
     *
     * @see http://docs.mongodb.org/manual/reference/connection-string/
     * @param string $uri           MongoDB connection string
     * @param array  $options       Additional connection string options
     * @param array  $driverOptions Driver-specific options
     */
    public function __construct($uri, array $options = array(), array $driverOptions = array())
    {
        $this->manager = new Manager($uri, $options, $driverOptions);
    }

    /**
     * Drop a database.
     *
     * @param string $databaseName
     * @return object Command result document
     */
    public function dropDatabase($databaseName)
    {
        $operation = new DropDatabase($databaseName);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * List databases.
     *
     * @see ListDatabases::__construct() for supported options
     * @return DatabaseInfoIterator
     */
    public function listDatabases(array $options = array())
    {
        $operation = new ListDatabases($options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Select a collection.
     *
     * If a write concern or read preference is not specified, the write concern
     * or read preference of the Client will be applied, respectively.
     *
     * @param string         $databaseName   Name of the database containing the collection
     * @param string         $collectionName Name of the collection to select
     * @param WriteConcern   $writeConcern   Default write concern to apply
     * @param ReadPreference $readPreference Default read preference to apply
     * @return Collection
     */
    public function selectCollection($databaseName, $collectionName, WriteConcern $writeConcern = null, ReadPreference $readPreference = null)
    {
        $namespace = $databaseName . '.' . $collectionName;
        // TODO: inherit from Manager options once PHPC-196 is implemented
        $writeConcern = $writeConcern ?: $this->writeConcern;
        $readPreference = $readPreference ?: $this->readPreference;

        return new Collection($this->manager, $namespace, $writeConcern, $readPreference);
    }

    /**
     * Select a database.
     *
     * If a write concern or read preference is not specified, the write concern
     * or read preference of the Client will be applied, respectively.
     *
     * @param string         $databaseName   Name of the database to select
     * @param WriteConcern   $writeConcern   Default write concern to apply
     * @param ReadPreference $readPreference Default read preference to apply
     * @return Database
     */
    public function selectDatabase($databaseName, WriteConcern $writeConcern = null, ReadPreference $readPreference = null)
    {
        // TODO: inherit from Manager options once PHPC-196 is implemented
        $writeConcern = $writeConcern ?: $this->writeConcern;
        $readPreference = $readPreference ?: $this->readPreference;

        return new Database($this->manager, $databaseName, $writeConcern, $readPreference);
    }
}
