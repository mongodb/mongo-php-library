<?php

namespace MongoDB;

use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Result;
use MongoDB\Driver\WriteConcern;

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
        $this->databaseName = $databaseName;
        $this->writeConcern = $writeConcern;
        $this->readPreference = $readPreference;
    }

    /**
     * Create a new collection explicitly.
     *
     * @see http://docs.mongodb.org/manual/reference/command/create/
     * @see http://docs.mongodb.org/manual/reference/method/db.createCollection/
     * @param string $collectionName
     * @param array  $options
     * @return Result
     */
    public function createCollection($collectionName, array $options = array())
    {
        // TODO
    }

    /**
     * Drop this database.
     *
     * @see http://docs.mongodb.org/manual/reference/command/dropDatabase/
     * @return Result
     */
    public function drop()
    {
        $command = new Command(array('dropDatabase' => 1));
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);

        return $this->manager->executeCommand($this->databaseName, $command, $readPreference);
    }

    /**
     * Drop a collection within this database.
     *
     * @param string $collectionName
     * @return Result
     */
    public function dropCollection($collectionName)
    {
        // TODO
    }

    /**
     * Returns information for all collections in this database.
     *
     * @see http://docs.mongodb.org/manual/reference/command/listCollections/
     * @param array $options
     * @return Result
     */
    public function listCollections(array $options = array())
    {
        // TODO
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
