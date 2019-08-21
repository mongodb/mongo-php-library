<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\ModifyCollection;
use function array_key_exists;

class ModifyCollectionFunctionalTest extends FunctionalTestCase
{
    public function testCollMod()
    {
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

        if (array_key_exists('raw', $result)) {
            /* Sharded environment, where we only assert if a shard had a successful update. For
             * non-primary shards that don't have chunks for the collection, the result contains a
             * "ns does not exist" error. */
            foreach ($result['raw'] as $shard) {
                if (array_key_exists('ok', $shard) && $shard['ok'] == 1) {
                    $this->assertSame(3, $shard['expireAfterSeconds_old']);
                    $this->assertSame(1000, $shard['expireAfterSeconds_new']);
                }
            }
        } else {
            $this->assertSame(3, $result['expireAfterSeconds_old']);
            $this->assertSame(1000, $result['expireAfterSeconds_new']);
        }
    }
}
