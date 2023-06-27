<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Update;

class UpdateTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$filter to have type "document" \(array or object\) but found ".+"/');
        new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, ['$set' => ['x' => 1]]);
    }

    /** @dataProvider provideInvalidUpdateValues */
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$update to have type "array or object" but found "[\w ]+"/');
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'arrayFilters' => $this->getInvalidArrayValues(),
            'bypassDocumentValidation' => $this->getInvalidBooleanValues(),
            'collation' => $this->getInvalidDocumentValues(),
            'hint' => $this->getInvalidHintValues(),
            'multi' => $this->getInvalidBooleanValues(),
            'session' => $this->getInvalidSessionValues(),
            'upsert' => $this->getInvalidBooleanValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @dataProvider provideEmptyUpdatePipelines
     */
    public function testConstructorMultiOptionProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"multi" option cannot be true unless $update has update operator(s) or non-empty pipeline');
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update, ['multi' => true]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'arrayFilters' => [['x' => 1]],
            'bypassDocumentValidation' => true,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'multi' => true,
            'upsert' => true,
            'let' => ['a' => 3],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['$set' => ['x' => 2]], $options);

        $expected = [
            'update' => $this->getCollectionName(),
            'bypassDocumentValidation' => true,
            'updates' => [
                [
                    'q' => ['x' => 1],
                    'u' => ['$set' => ['x' => 2]],
                    'multi' => true,
                    'upsert' => true,
                    'arrayFilters' => [['x' => 1]],
                    'hint' => '_id_',
                    'collation' => (object) ['locale' => 'fr'],
                ],
            ],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
