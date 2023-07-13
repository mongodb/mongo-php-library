<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Collection functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    protected Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
    }
}
