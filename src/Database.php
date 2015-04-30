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
use MongoDB\Model\CollectionInfoCommandIterator;
use MongoDB\Model\CollectionInfoLegacyIterator;

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
     * @see http://docs.mongodb.org/manual/reference/command/dropDatabase/
     * @return Cursor
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
     * @see http://docs.mongodb.org/manual/reference/command/listCollections/
     * @param array $options
     * @return CollectionInfoIterator
     */
    public function listCollections(array $options = array())
    {
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
        $server = $this->manager->selectServer($readPreference);

        return (FeatureDetection::isSupported($server, FeatureDetection::API_LISTCOLLECTIONS_CMD))
            ? $this->listCollectionsCommand($server, $options)
            : $this->listCollectionsLegacy($server, $options);
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

    /**
     * Returns information for all collections in this database using the
     * listCollections command.
     *
     * @param Server $server
     * @param array  $options
     * @return CollectionInfoCommandIterator
     */
    private function listCollectionsCommand(Server $server, array $options = array())
    {
        $command = new Command(array('listCollections' => 1) + $options);
        $cursor = $server->executeCommand($this->databaseName, $command);
        $cursor->setTypeMap(array('document' => 'array'));

        return new CollectionInfoCommandIterator($cursor);
    }

    /**
     * Returns information for all collections in this database by querying the
     * "system.namespaces" collection (MongoDB <2.8).
     *
     * @param Server $server
     * @param array  $options
     * @return CollectionInfoLegacyIterator
     * @throws InvalidArgumentException if the filter option is neither an array
     *                                  nor object, or if filter.name is not a
     *                                  string.
     */
    private function listCollectionsLegacy(Server $server, array $options = array())
    {
        $filter = array_key_exists('filter', $options) ? $options['filter'] : array();

        if ( ! is_array($filter) && ! is_object($filter)) {
            throw new InvalidArgumentException(sprintf('Expected filter to be array or object, %s given', gettype($filter)));
        }

        if (array_key_exists('name', (array) $filter)) {
            $filter = (array) $filter;

            if ( ! is_string($filter['name'])) {
                throw new InvalidArgumentException(sprintf('Filter "name" must be a string for MongoDB <2.8, %s given', gettype($filter['name'])));
            }

            $filter['name'] = $this->databaseName . '.' . $filter['name'];
        }

        $namespace = $this->databaseName . '.system.namespaces';
        $query = new Query($filter);
        $cursor = $server->executeQuery($namespace, $query);
        $cursor->setTypeMap(array('document' => 'array'));

        return new CollectionInfoLegacyIterator($cursor);
    }
}
