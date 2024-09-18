<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\GridFS\Bucket;
use MongoDB\GridFS\CollectionWrapper;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\TestCase;
use ReflectionMethod;

use function array_merge;
use function fclose;
use function fopen;
use function fwrite;
use function hash_init;
use function str_repeat;

/**
 * Unit tests for the Bucket class.
 */
class BucketTest extends TestCase
{
    private Manager $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = new Manager('mongodb://server.test:27017');
    }

    /** @doesNotPerformAssertions */
    public function testValidConstructorOptions(): void
    {
        new Bucket($this->manager, $this->getDatabaseName(), [
            'bucketName' => 'test',
            'chunkSizeBytes' => 8192,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY, 1000),
            'disableMD5' => true,
        ]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Bucket($this->manager, $this->getDatabaseName(), $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'bucketName' => self::getInvalidStringValues(true),
            'chunkSizeBytes' => self::getInvalidIntegerValues(true),
            'codec' => self::getInvalidDocumentCodecValues(),
            'disableMD5' => self::getInvalidBooleanValues(true),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testConstructorShouldRequireChunkSizeBytesOptionToBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "chunkSizeBytes" option to be >= 1, 0 given');
        new Bucket($this->manager, $this->getDatabaseName(), ['chunkSizeBytes' => 0]);
    }

    public function testConstructorWithCodecAndTypeMapOptions(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        new Bucket($this->manager, $this->getDatabaseName(), $options);
    }

    public static function provideInputDataAndExpectedChunks()
    {
        return [
            ['', 0],
            ['foobar', 1],
            [str_repeat('a', 261120), 1],
            [str_repeat('a', 261121), 2],
            [str_repeat('a', 522240), 2],
            [str_repeat('a', 522241), 3],
            [str_repeat('foobar', 43520), 1],
            [str_repeat('foobar', 43521), 2],
            [str_repeat('foobar', 87040), 2],
            [str_repeat('foobar', 87041), 3],
        ];
    }

    /** @dataProvider provideInvalidStreamValues */
    public function testDownloadToStreamShouldRequireDestinationStream($destination): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->expectException(InvalidArgumentException::class);
        $bucket->downloadToStream('id', $destination);
    }

    public static function provideInvalidStreamValues(): array
    {
        return self::wrapValuesForDataProvider(self::getInvalidStreamValues());
    }

    /** @dataProvider provideInvalidStreamValues */
    public function testDownloadToStreamByNameShouldRequireDestinationStream($destination): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->expectException(InvalidArgumentException::class);
        $bucket->downloadToStreamByName('filename', $destination);
    }

    public static function provideNonexistentFilenameAndRevision()
    {
        return [
            ['filename', 2],
            ['filename', -3],
            ['nonexistent-filename', 0],
            ['nonexistent-filename', -1],
        ];
    }

    public function testGetBucketNameWithCustomValue(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName(), ['bucketName' => 'custom_fs']);
        $this->assertEquals('custom_fs', $bucket->getBucketName());
    }

    public function testGetBucketNameWithDefaultValue(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->assertEquals('fs', $bucket->getBucketName());
    }

    public function testGetChunksCollection(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $chunksCollection = $bucket->getChunksCollection();

        $this->assertInstanceOf(Collection::class, $chunksCollection);
        $this->assertEquals('fs.chunks', $chunksCollection->getCollectionName());
    }

    public function testGetChunkSizeBytesWithCustomValue(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName(), ['chunkSizeBytes' => 8192]);

        $this->assertEquals(8192, $bucket->getChunkSizeBytes());
    }

    public function testGetChunkSizeBytesWithDefaultValue(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->assertEquals(261120, $bucket->getChunkSizeBytes());
    }

    public function testGetDatabaseName(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->assertEquals($this->getDatabaseName(), $bucket->getDatabaseName());
    }

    public static function provideInvalidGridFSStreamValues(): array
    {
        return self::wrapValuesForDataProvider(array_merge(self::getInvalidStreamValues(), [fopen('php://temp', 'r')]));
    }

    /** @dataProvider provideInvalidGridFSStreamValues */
    public function testGetFileIdForStreamShouldRequireGridFSStreamResource($stream): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->expectException(InvalidArgumentException::class);
        $bucket->getFileIdForStream($stream);
    }

    public function testGetFilesCollection(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $filesCollection = $bucket->getFilesCollection();

        $this->assertInstanceOf(Collection::class, $filesCollection);
        $this->assertEquals('fs.files', $filesCollection->getCollectionName());
    }

    public function testResolveStreamContextForRead(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $stream = $bucket->openUploadStream('filename');
        fwrite($stream, 'foobar');
        fclose($stream);
        $method = new ReflectionMethod($bucket, 'resolveStreamContext');
        $context = $method->invokeArgs($bucket, ['gridfs://bucket/filename', 'rb', []]);

        $this->assertIsArray($context);
        $this->assertArrayHasKey('collectionWrapper', $context);
        $this->assertInstanceOf(CollectionWrapper::class, $context['collectionWrapper']);
        $this->assertArrayHasKey('file', $context);
        $this->assertIsObject($context['file']);
        $this->assertInstanceOf(ObjectId::class, $context['file']->_id);
        $this->assertSame('filename', $context['file']->filename);
    }

    public function testResolveStreamContextForWrite(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName());
        $method = new ReflectionMethod($bucket, 'resolveStreamContext');
        $context = $method->invokeArgs($bucket, ['gridfs://bucket/filename', 'wb', []]);

        $this->assertIsArray($context);
        $this->assertArrayHasKey('collectionWrapper', $context);
        $this->assertInstanceOf(CollectionWrapper::class, $context['collectionWrapper']);
        $this->assertArrayHasKey('filename', $context);
        $this->assertSame('filename', $context['filename']);
        $this->assertArrayHasKey('options', $context);
        $this->assertSame(['chunkSizeBytes' => 261120, 'disableMD5' => false], $context['options']);
    }

    /**
     * Return a list of invalid stream values.
     */
    private static function getInvalidStreamValues(): array
    {
        return [null, 123, 'foo', [], hash_init('md5')];
    }
}
