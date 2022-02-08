<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\ModifyCollection;

class ModifyCollectionFunctionalTest extends FunctionalTestCase
{
    /**
     * @group matrix-testing-exclude-server-4.2-driver-4.0-topology-sharded_cluster
     * @group matrix-testing-exclude-server-4.4-driver-4.0-topology-sharded_cluster
     * @group matrix-testing-exclude-server-5.0-driver-4.0-topology-sharded_cluster
     */
    public function testCollMod(): void
    {
        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Sharded clusters may report result inconsistently');
        }

        $this->createCollection();

        $indexes = [['key' => ['lastAccess' => 1], 'expireAfterSeconds' => 3]];
        $createIndexes = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createIndexes->execute($this->getPrimaryServer());

        $modifyCollection = new ModifyCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['index' => ['keyPattern' => ['lastAccess' => 1], 'expireAfterSeconds' => 1000]],
            ['typeMap' => ['root' => 'array', 'document' => 'array']]
        );
        $result = $modifyCollection->execute($this->getPrimaryServer());

        $this->assertSame(3, $result['expireAfterSeconds_old']);
        $this->assertSame(1000, $result['expireAfterSeconds_new']);
    }
}
