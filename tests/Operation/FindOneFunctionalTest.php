<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\FindOne;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;
use PHPUnit\Framework\Attributes\DataProvider;

class FindOneFunctionalTest extends FunctionalTestCase
{
    #[DataProvider('provideTypeMapOptionsAndExpectedDocument')]
    public function testTypeMapOption(array $typeMap, $expectedDocument): void
    {
        $this->createFixtures(1);

        $operation = new FindOne($this->getDatabaseName(), $this->getCollectionName(), [], ['typeMap' => $typeMap]);
        $document = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($expectedDocument, $document);
    }

    public static function provideTypeMapOptionsAndExpectedDocument()
    {
        return [
            [
                ['root' => 'array', 'document' => 'array'],
                ['_id' => 1, 'x' => ['foo' => 'bar']],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
            ],
        ];
    }

    public function testCodecOption(): void
    {
        $this->createFixtures(1);

        $codec = new TestDocumentCodec();

        $operation = new FindOne($this->getDatabaseName(), $this->getCollectionName(), [], ['codec' => $codec]);
        $document = $operation->execute($this->getPrimaryServer());

        $this->assertEquals(TestObject::createDecodedForFixture(1), $document);
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
