<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Operation functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->dropCollection();
    }

    public function tearDown(): void
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollection();

        parent::tearDown();
    }

    protected function createDefaultReadConcern()
    {
        return new ReadConcern();
    }

    protected function createDefaultWriteConcern()
    {
        return new WriteConcern(-2);
    }

    protected function createSession()
    {
        return $this->manager->startSession();
    }
}
