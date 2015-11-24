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
    private $uri;

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
    public function __construct($uri = 'mongodb://localhost:27017', array $options = [], array $driverOptions = [])
    {
        $this->manager = new Manager($uri, $options, $driverOptions);
        $this->uri = (string) $uri;
    }

    /**
     * Return the connection string (i.e. URI).
     *
     * @param string
     */
    public function __toString()
    {
        return $this->uri;
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
    public function listDatabases(array $options = [])
    {
        $operation = new ListDatabases($options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Select a collection.
     *
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for collection operations. Defaults to the Client's
     *    read preference.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for collection operations. Defaults to the Client's write
     *    concern.
     *
     * @param string $databaseName   Name of the database containing the collection
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @return Collection
     */
    public function selectCollection($databaseName, $collectionName, array $options = [])
    {
        return new Collection($this->manager, $databaseName . '.' . $collectionName, $options);
    }

    /**
     * Select a database.
     *
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to the Client's read preference.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    the Client's write concern.
     *
     * @param string $databaseName Name of the database to select
     * @param array  $options      Database constructor options
     * @return Database
     */
    public function selectDatabase($databaseName, array $options = [])
    {
        return new Database($this->manager, $databaseName, $options);
    }
}
