<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\ModifyCollection;
use PHPUnit\Framework\Attributes\Group;

class ModifyCollectionFunctionalTest extends FunctionalTestCase
{
    #[Group('matrix-testing-exclude-server-4.2-driver-4.0-topology-sharded_cluster')]
    #[Group('matrix-testing-exclude-server-4.4-driver-4.0-topology-sharded_cluster')]
    #[Group('matrix-testing-exclude-server-5.0-driver-4.0-topology-sharded_cluster')]
    public function testCollMod(): void
    {
        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Sharded clusters may report result inconsistently');
        }

        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $indexes = [['key' => ['lastAccess' => 1], 'expireAfterSeconds' => 3]];
        $createIndexes = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createIndexes->execute($this->getPrimaryServer());

        $modifyCollection = new ModifyCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['index' => ['keyPattern' => ['lastAccess' => 1], 'expireAfterSeconds' => 1000]],
            ['typeMap' => ['root' => 'array', 'document' => 'array']],
        );
        $result = $modifyCollection->execute($this->getPrimaryServer());

        $this->assertSame(3, $result['expireAfterSeconds_old']);
        $this->assertSame(1000, $result['expireAfterSeconds_new']);
    }
}
