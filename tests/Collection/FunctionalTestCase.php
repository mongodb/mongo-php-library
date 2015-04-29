<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Collection functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    protected $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getNamespace());
        $this->dropCollectionIfItExists($this->collection);
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollectionIfItExists($this->collection);
    }

    /**
     * Drop the collection if it exists.
     *
     * @param Collection $collection
     */
    protected function dropCollectionIfItExists(Collection $collection)
    {
        $database = new Database($this->manager, $collection->getDatabaseName());
        $collections = $database->listCollections(array('filter' => array('name' => $collection->getCollectionName())));

        if (iterator_count($collections) > 0) {
            $this->assertCommandSucceeded($collection->drop());
        }
    }
}
