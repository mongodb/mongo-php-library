<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\ListIndexes;
use MongoDB\Operation\ModifyCollection;
use PHPUnit\Framework\Attributes\Group;

use function iterator_to_array;
use function json_encode;

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
        $modifyCollection->execute($this->getPrimaryServer());

        $listIndexes = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $listIndexes->execute($this->getPrimaryServer());
        $indexes = iterator_to_array($indexes);
        $this->assertCount(2, $indexes);

        foreach ($indexes as $index) {
            switch ($index['key']) {
                case ['_id' => 1]:
                    break;
                case ['lastAccess' => 1]:
                    $this->assertSame(1000, $index['expireAfterSeconds']);
                    break;
                default:
                    $this->fail('Unexpected index key: ' . json_encode($index->key));
            }
        }
    }
}
