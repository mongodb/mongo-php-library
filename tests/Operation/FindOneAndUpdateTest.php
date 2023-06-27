<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\FindOneAndUpdate;

class FindOneAndUpdateTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), $filter, []);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], $update);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @dataProvider provideEmptyUpdatePipelines
     */
    public function testConstructorUpdateArgumentProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $update');
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], $update);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], ['$set' => ['x' => 1]], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'projection' => $this->getInvalidDocumentValues(),
            'returnDocument' => $this->getInvalidIntegerValues(),
        ]);
    }

    /** @dataProvider provideInvalidConstructorReturnDocumentOptions */
    public function testConstructorReturnDocumentOption($returnDocument): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], [], ['returnDocument' => $returnDocument]);
    }

    public function provideInvalidConstructorReturnDocumentOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 3]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'arrayFilters' => [['x' => 1]],
            'bypassDocumentValidation' => true,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
            'sort' => ['x' => 1],
            'upsert' => true,
            'let' => ['a' => 3],
            // Intentionally omitted options
            'projection' => ['_id' => 0],
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];
        $operation = new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), ['y' => 2], ['$set' => ['x' => 2]], $options);

        $expected = [
            'findAndModify' => $this->getCollectionName(),
            'new' => true,
            'upsert' => true,
            'collation' => (object) ['locale' => 'fr'],
            'fields' => (object) ['_id' => 0],
            'let' => (object) ['a' => 3],
            'query' => (object) ['y' => 2],
            'sort' => (object) ['x' => 1],
            'update' => (object) ['$set' => ['x' => 2]],
            'arrayFilters' =>  [['x' => 1]],
            'bypassDocumentValidation' => true,
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
