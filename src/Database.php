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
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Model\CollectionInfoIterator;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DropCollection;
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
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to the Manager's read preference.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    the Manager's write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Database options
     * @throws InvalidArgumentException
     */
    public function __construct(Manager $manager, $databaseName, array $options = [])
    {
        if (strlen($databaseName) < 1) {
            throw new InvalidArgumentException('$databaseName is invalid: ' . $databaseName);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw new InvalidArgumentTypeException('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw new InvalidArgumentTypeException('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->manager = $manager;
        $this->databaseName = (string) $databaseName;
        $this->readPreference = isset($options['readPreference']) ? $options['readPreference'] : $this->manager->getReadPreference();
        $this->writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : $this->manager->getWriteConcern();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @param array
     */
    public function __debugInfo()
    {
        return [
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'readPreference' => $this->readPreference,
            'writeConcern' => $this->writeConcern,
        ];
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
     * @see CreateCollection::__construct() for supported options
     * @param string $collectionName
     * @param array  $options
     * @return object Command result document
     */
    public function createCollection($collectionName, array $options = [])
    {
        $operation = new CreateCollection($this->databaseName, $collectionName, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
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
     * @param string $collectionName
     * @return object Command result document
     */
    public function dropCollection($collectionName)
    {
        $operation = new DropCollection($this->databaseName, $collectionName);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
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
    public function listCollections(array $options = [])
    {
        $operation = new ListCollections($this->databaseName, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Select a collection within this database.
     *
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for collection operations. Defaults to the
     *    Database's read preference.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for collection operations. Defaults to the Database's write
     *    concern.
     *
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @return Collection
     */
    public function selectCollection($collectionName, array $options = [])
    {
        if ( ! isset($options['readPreference'])) {
            $options['readPreference'] = $this->readPreference;
        }

        if ( ! isset($options['writeConcern'])) {
            $options['writeConcern'] = $this->writeConcern;
        }

        return new Collection($this->manager, $this->databaseName . '.' . $collectionName, $options);
    }

    /**
     * Get a clone of this database with different options.
     *
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to this Database's read preference.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    this Database's write concern.
     *
     * @param array $options Database constructor options
     * @return Database
     */
    public function withOptions(array $options = [])
    {
        if ( ! isset($options['readPreference'])) {
            $options['readPreference'] = $this->readPreference;
        }

        if ( ! isset($options['writeConcern'])) {
            $options['writeConcern'] = $this->writeConcern;
        }

        return new Database($this->manager, $this->databaseName, $options);
    }
}
