<?php

namespace MongoDB;

use MongoDB\Driver\Manager;
use MongoDB\Database;
use MongoDB\Collection;

class Client
{
    private $manager;
    private $wc;
    private $rp;


    /**
     * Constructs new Client instance
     *
     * This is the suggested main entry point using phongo.
     * It acts as a bridge to access individual databases and collection tools
     * which are provided in this namespace.
     *
     * @param Manager        $uri            The MongoDB URI to connect to
     * @param WriteConcern   $options        URI Options
     * @param ReadPreference $driverOptions  Driver specific options
     */
    public function __construct($uri, $options, $driverOptions)
    {
        $this->manager = new Manager($uri, $options, $driverOptions);
    }

    /**
     * Select a database
     *
     * It acts as a bridge to access specific database commands
     *
     * @param string         $databaseName     The database to select
     * @param WriteConcern   $writeConcern     Default Write Concern to apply
     * @param ReadPreference $readPreferences  Default Read Preferences to apply
     */
    public function selectDatabase($databaseName, WriteConcern $writeConcern = null, ReadPreference $readPreferences = null)
    {
        return new Database($this->manager, $databaseName, $writeConcern, $readPreferences);
    }

    /**
     * Select a specific collection in a database
     *
     * It acts as a bridge to access specific collection commands
     *
     * @param string         $databaseName     The database where the $collectionName exists
     * @param string         $collectionName   The collection to select
     * @param WriteConcern   $writeConcern     Default Write Concern to apply
     * @param ReadPreference $readPreferences  Default Read Preferences to apply
     */
    public function selectCollection($databaseName, $collectionName, WriteConcern $writeConcern = null, ReadPreference $readPreferences = null)
    {
        return new Collection($this->manager, "{$databaseName}.{$collectionName}", $writeConcern, $readPreferences);
    }

}

