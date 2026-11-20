<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\BSON\MinKey;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\GridFS\CollectionWrapper;
use MongoDB\GridFS\WritableStream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use ReflectionMethod;

use function str_repeat;

/**
 * Functional tests for the internal WritableStream class.
 */
class WritableStreamFunctionalTest extends FunctionalTestCase
{
    private CollectionWrapper $collectionWrapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionWrapper = new CollectionWrapper($this->manager, $this->getDatabaseName(), 'fs');
    }

    #[DoesNotPerformAssertions]
    public function testValidConstructorOptions(): void
    {
        new WritableStream($this->collectionWrapper, 'filename', [
            '_id' => 'custom-id',
            'chunkSizeBytes' => 2,
            'metadata' => ['foo' => 'bar'],
        ]);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new WritableStream($this->collectionWrapper, 'filename', $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'chunkSizeBytes' => self::getInvalidIntegerValues(true),
            'metadata' => self::getInvalidDocumentValues(),
        ]);
    }

    public function testConstructorShouldRequireChunkSizeBytesOptionToBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "chunkSizeBytes" option to be >= 1, 0 given');
        new WritableStream($this->collectionWrapper, 'filename', ['chunkSizeBytes' => 0]);
    }

    public function testWriteBytesAlwaysUpdatesFileSize(): void
    {
        $stream = new WritableStream($this->collectionWrapper, 'filename', ['chunkSizeBytes' => 1024]);

        $this->assertSame(0, $stream->getSize());
        $this->assertSame(512, $stream->writeBytes(str_repeat('a', 512)));
        $this->assertSame(512, $stream->getSize());
        $this->assertSame(512, $stream->writeBytes(str_repeat('a', 512)));
        $this->assertSame(1024, $stream->getSize());
        $this->assertSame(512, $stream->writeBytes(str_repeat('a', 512)));
        $this->assertSame(1536, $stream->getSize());

        $stream->close();
        $this->assertSame(1536, $stream->getSize());
    }

    public function testAbortWithQueryOperatorIdDoesNotDeleteOtherFilesChunks(): void
    {
        $otherId = $this->bucket->uploadFromStream('file1', self::createStream('foobar'));

        $stream = new WritableStream($this->collectionWrapper, 'file2', [
            '_id' => ['$gt' => new MinKey()],
            'chunkSizeBytes' => 2,
        ]);
        $stream->writeBytes('abcd');

        (new ReflectionMethod($stream, 'abort'))->invoke($stream);

        $this->assertCollectionCount($this->chunksCollection, 1);

        $chunk = $this->chunksCollection->findOne([], ['typeMap' => ['root' => 'array']]);
        $this->assertSameObjectId($otherId, $chunk['files_id']);
    }
}
