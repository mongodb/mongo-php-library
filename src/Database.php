<?php

namespace MongoDB;

use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\CollectionInfoIterator;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\ListCollections;

class Database
{
    private $databaseName;
    private $manager;
    private $readPreference;
    private $writeConcern;

    /**
     * Constructs new Database instance.
     *
     * This class provides methods for database-specific operations and serves
     * as a gateway for accessing collections.
     *
     * @param Manager        $manager        Manager instance from the driver
     * @param string         $databaseName   Database name
     * @param WriteConcern   $writeConcern   Default write concern to apply
     * @param ReadPreference $readPreference Default read preference to apply
     */
    public function __construct(Manager $manager, $databaseName, WriteConcern $writeConcern = null, ReadPreference $readPreference = null)
    {
        $this->manager = $manager;
        $this->databaseName = (string) $databaseName;
        $this->writeConcern = $writeConcern;
        $this->readPreference = $readPreference;
    }

    /**
     * Return the database name.
     *
     * @param string
     */
    public function __toString()
    {
        return $this->databaseName;
    }

    /**
     * Create a new collection explicitly.
     *
     * @see http://docs.mongodb.org/manual/reference/command/create/
     * @see http://docs.mongodb.org/manual/reference/method/db.createCollection/
     * @param string $collectionName
     * @param array  $options
     * @return Cursor
     */
    public function createCollection($collectionName, array $options = array())
    {
        $collectionName = (string) $collectionName;
        $command = new Command(array('create' => $collectionName) + $options);
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);

        return $this->manager->executeCommand($this->databaseName, $command, $readPreference);
    }

    /**
     * Drop this database.
     *
     * @return object Command result document
     */
    public function drop()
    {
        $operation = new DropDatabase($this->databaseName);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Drop a collection within this database.
     *
     * @see http://docs.mongodb.org/manual/reference/command/drop/
     * @param string $collectionName
     * @return Cursor
     */
    public function dropCollection($collectionName)
    {
        $collectionName = (string) $collectionName;
        $command = new Command(array('drop' => $collectionName));
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);

        return $this->manager->executeCommand($this->databaseName, $command, $readPreference);
    }

    /**
     * Returns the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Returns information for all collections in this database.
     *
     * @see ListCollections::__construct() for supported options
     * @param array $options
     * @return CollectionInfoIterator
     */
    public function listCollections(array $options = array())
    {
        $operation = new ListCollections($this->databaseName, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Select a collection within this database.
     *
     * If a write concern or read preference is not specified, the write concern
     * or read preference of the Database will be applied, respectively.
     *
     * @param string         $collectionName Name of the collection to select
     * @param WriteConcern   $writeConcern   Default write concern to apply
     * @param ReadPreference $readPreference Default read preference to apply
     * @return Collection
     */
    public function selectCollection($collectionName, WriteConcern $writeConcern = null, ReadPreference $readPreference = null)
    {
        $namespace = $this->databaseName . '.' . $collectionName;
        $writeConcern = $writeConcern ?: $this->writeConcern;
        $readPreference = $readPreference ?: $this->readPreference;

        return new Collection($this->manager, $namespace, $writeConcern, $readPreference);
    }
}
