<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Collection;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\DropCollection;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Operation functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dropCollection();
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollection();
    }

    protected function createDefaultReadConcern()
    {
        return new ReadConcern;
    }

    protected function createDefaultWriteConcern()
    {
        return new WriteConcern(-2);
    }

    protected function createSession()
    {
        return $this->manager->startSession();
    }

    private function dropCollection()
    {
        $options = version_compare($this->getServerVersion(), '3.4.0', '>=')
            ? ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]
            : [];

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
        $operation->execute($this->getPrimaryServer());
    }
}
