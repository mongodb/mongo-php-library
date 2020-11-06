<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * Base class for Collection functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var Collection */
    protected $collection;

    private function doSetUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $this->dropCollection();
    }

    private function doTearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollection();

        parent::tearDown();
    }
}
