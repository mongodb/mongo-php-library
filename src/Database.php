<?php

namespace MongoDB;

use MongoDB\Collection;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Result;

class Database
{
    private $manager;
    private $ns;
    private $wc;
    private $rp;

    private $dbname;

    /**
     * Constructs new Database instance
     *
     * It acts as a bridge for database specific operations.
     *
     * @param Manager        $manager The phongo Manager instance
     * @param string         $dbname  Fully Qualified database name
     * @param WriteConcern   $wc      The WriteConcern to apply to writes
     * @param ReadPreference $rp      The ReadPreferences to apply to reads
     */
    public function __construct(Manager $manager, $databaseName, WriteConcern $wc = null, ReadPreference $rp = null)
    {
        $this->manager = $manager;
        $this->dbname  = $dbname;
        $this->wc = $wc;
        $this->rp = $rp;
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
     * @return Result
     */
    public function drop()
    {
        // TODO
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
     * Select a specific collection in this database
     *
     * It acts as a bridge to access specific collection commands
     *
     * @param string         $collectionName   The collection to select
     * @param WriteConcern   $writeConcern     Default Write Concern to apply
     * @param ReadPreference $readPreferences  Default Read Preferences to apply
     */
    public function selectCollection($collectionName, WriteConcern $writeConcern = null, ReadPreference $readPreferences = null)
    {
        return new Collection($this->manager, "{$this->dbname}.{$collectionName}", $writeConcern, $readPreferences);
    }

}


