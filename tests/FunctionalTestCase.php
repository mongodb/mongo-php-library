<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Manager;

abstract class FunctionalTestCase extends TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager($this->getUri());
    }
}
