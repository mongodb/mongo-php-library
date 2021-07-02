<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Collection functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    /** @var Collection */
    protected $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

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
}
