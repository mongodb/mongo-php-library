<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Collection;
use MongoDB\Driver\WriteConcern;
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

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $this->dropCollection();
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollection();
    }

    private function dropCollection()
    {
        $options = version_compare($this->getServerVersion(), '3.4.0', '>=')
            ? ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]
            : [];

        $this->collection->drop($options);
    }
}
