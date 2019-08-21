<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * Base class for Operation functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->dropCollection();
    }

    private function doTearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollection();
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
