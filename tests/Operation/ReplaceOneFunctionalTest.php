<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\ReplaceOne;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;

class ReplaceOneFunctionalTest extends FunctionalTestCase
{
    public function testReplaceOneWithCodec(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new ReplaceOne(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['x' => 1],
                    TestObject::createForFixture(1),
                    ['codec' => new TestDocumentCodec()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertEquals(
                    (object) [
                        '_id' => 1,
                        'x' => (object) ['foo' => 'bar'],
                        'encoded' => true,
                    ],
                    $event['started']->getCommand()->updates[0]->u ?? null,
                );
            },
        );
    }
}
