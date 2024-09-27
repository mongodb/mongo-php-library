<?php

/* Enable strict types to disable type coercion for arguments. Without this, the
 * non-int test values 3.14 and true would be silently coerced to integers,
 * which is not what we're expecting to test here. */
declare(strict_types=1);

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Delete;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class DeleteTest extends TestCase
{
    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 0);
    }

    #[DataProvider('provideInvalidIntegerValues')]
    public function testConstructorLimitArgumentMustBeInt($limit): void
    {
        $this->expectException(TypeError::class);
        new Delete($this->getDatabaseName(), $this->getCollectionName(), [], $limit);
    }

    #[DataProvider('provideInvalidLimitValues')]
    public function testConstructorLimitArgumentMustBeOneOrZero($limit): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$limit must be 0 or 1');
        new Delete($this->getDatabaseName(), $this->getCollectionName(), [], $limit);
    }

    public static function provideInvalidLimitValues()
    {
        return self::wrapValuesForDataProvider([-1, 2]);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Delete($this->getDatabaseName(), $this->getCollectionName(), [], 1, $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'collation' => self::getInvalidDocumentValues(),
            'hint' => self::getInvalidHintValues(),
            'let' => self::getInvalidDocumentValues(),
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'collation' => ['locale' => 'fr'],
            'hint' => '_id_',
            'let' => ['a' => 1],
            'comment' => 'explain me',
            // Intentionally omitted options
            'writeConcern' => new WriteConcern(0),
        ];
        $operation = new Delete($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], 0, $options);

        $expected = [
            'delete' => $this->getCollectionName(),
            'deletes' => [
                [
                    'q' => ['x' => 1],
                    'limit' => 0,
                    'collation' => (object) ['locale' => 'fr'],
                    'hint' => '_id_',
                ],
            ],
            'comment' => 'explain me',
            'let' => (object) ['a' => 1],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
