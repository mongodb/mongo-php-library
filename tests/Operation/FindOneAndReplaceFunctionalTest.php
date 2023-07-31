<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;

class FindOneAndReplaceFunctionalTest extends FunctionalTestCase
{
    public function testFindAndReplaceOneWithCodec(): void
    {
        $this->createFixtures(1);

        (new CommandObserver())->observe(
            function (): void {
                $replaceObject = TestObject::createForFixture(1);
                $replaceObject->x->foo = 'baz';

                $operation = new FindOneAndReplace(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    $replaceObject,
                    ['codec' => new TestDocumentCodec()],
                );

                $this->assertEquals(
                    TestObject::createDecodedForFixture(1),
                    $operation->execute($this->getPrimaryServer()),
                );
            },
            function (array $event): void {
                $this->assertEquals(
                    (object) [
                        '_id' => 1,
                        'x' => (object) ['foo' => 'baz'],
                        'encoded' => true,
                    ],
                    $event['started']->getCommand()->update ?? null,
                );
            },
        );
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert(TestObject::createDocument($i));
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
