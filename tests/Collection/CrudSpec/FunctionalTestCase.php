<?php

namespace MongoDB\Tests\Collection\CrudSpec;

use MongoDB\Driver\BulkWrite;
use MongoDB\Tests\Collection\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Collection CRUD spec functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    protected function createFixtures($n)
    {
        $bulkWrite = new BulkWrite(true);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert(array(
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ));
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
