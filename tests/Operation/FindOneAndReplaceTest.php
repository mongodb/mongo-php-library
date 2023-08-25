<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\FindOneAndReplace;

class FindOneAndReplaceTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), $filter, []);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], $replacement);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @doesNotPerformAssertions
     */
    public function testConstructorReplacementArgument($replacement): void
    {
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], $replacement);
    }

    /** @dataProvider provideUpdateDocuments */
    public function testConstructorReplacementArgumentProhibitsUpdateDocument($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $replacement is an update operator');
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], $replacement);
    }

    /**
     * @dataProvider provideUpdatePipelines
     * @dataProvider provideEmptyUpdatePipelinesExcludingArray
     */
    public function testConstructorReplacementArgumentProhibitsUpdatePipeline($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#(\$replacement is an update pipeline)|(Expected \$replacement to have type "document" \(array or object\))#');
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], $replacement);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'codec' => $this->getInvalidDocumentCodecValues(),
            'projection' => $this->getInvalidDocumentValues(),
            'returnDocument' => $this->getInvalidIntegerValues(true),
        ]);
    }

    /** @dataProvider provideInvalidConstructorReturnDocumentOptions */
    public function testConstructorReturnDocumentOption($returnDocument): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], [], ['returnDocument' => $returnDocument]);
    }

    public function provideInvalidConstructorReturnDocumentOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 3]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'bypassDocumentValidation' => true,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'fields' => ['_id' => 0],
            'hint' => '_id_',
            'maxTimeMS' => 100,
            'projection' => ['_id' => 0],
            'sort' => ['x' => 1],
            'let' => ['a' => 3],
            // Intentionally omitted options
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];
        $operation = new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), ['y' => 2], ['y' => 3], $options);

        $expected = [
            'findAndModify' => $this->getCollectionName(),
            'new' => true,
            'collation' => (object) ['locale' => 'fr'],
            'fields' => (object) ['_id' => 0],
            'let' => (object) ['a' => 3],
            'query' => (object) ['y' => 2],
            'sort' => (object) ['x' => 1],
            'update' => (object) ['y' => 3],
            'bypassDocumentValidation' => true,
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
