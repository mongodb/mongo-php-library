<?php

namespace MongoDB\Tests\Database;

use MongoDB\Database;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Database functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    protected Database $database;

    public function setUp(): void
    {
        parent::setUp();

        $this->database = new Database($this->manager, $this->getDatabaseName());
        $this->database->drop();
    }
}
