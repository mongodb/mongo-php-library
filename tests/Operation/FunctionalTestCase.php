<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadPreference;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Operation functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    public function getPrimaryServer()
    {
        return $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));
    }
}
