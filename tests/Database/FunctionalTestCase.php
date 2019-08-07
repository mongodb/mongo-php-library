<?php

namespace MongoDB\Tests\Database;

use MongoDB\Database;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * Base class for Database functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var Database */
    protected $database;

    private function doSetUp()
    {
        parent::setUp();

        $this->database = new Database($this->manager, $this->getDatabaseName());
        $this->database->drop();
    }
}
