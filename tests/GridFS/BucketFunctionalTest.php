<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\BSON\Binary;
use MongoDB\Collection;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\GridFS\Bucket;
use MongoDB\GridFS\Exception\CorruptFileException;
use MongoDB\GridFS\Exception\FileNotFoundException;
use MongoDB\GridFS\Exception\StreamException;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\IndexInfo;
use MongoDB\Operation\ListIndexes;

use function array_merge;
use function call_user_func;
use function current;
use function exec;
use function fclose;
use function fopen;
use function fread;
use function fwrite;
use function hash_init;
use function implode;
use function is_callable;
use function min;
use function sprintf;
use function str_repeat;
use function stream_get_contents;
use function strlen;
use function strncasecmp;
use function substr;

use const PHP_EOL;
use const PHP_OS;
use const PHP_VERSION_ID;

/**
 * Functional tests for the Bucket class.
 */
class BucketFunctionalTest extends FunctionalTestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testValidConstructorOptions(): void
    {
        new Bucket($this->manager, $this->getDatabaseName(), [
            'bucketName' => 'test',
            'chunkSizeBytes' => 8192,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY, 1000),
        ]);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Bucket($this->manager, $this->getDatabaseName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidStringValues(true) as $value) {
            $options[][] = ['bucketName' => $value];
        }

        foreach ($this->getInvalidIntegerValues(true) as $value) {
            $options[][] = ['chunkSizeBytes' => $value];
        }

        foreach ($this->getInvalidBooleanValues(true) as $value) {
            $options[][] = ['disableMD5' => $value];
        }

        foreach ($this->getInvalidReadConcernValues() as $value) {
            $options[][] = ['readConcern' => $value];
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    public function testConstructorShouldRequireChunkSizeBytesOptionToBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "chunkSizeBytes" option to be >= 1, 0 given');
        new Bucket($this->manager, $this->getDatabaseName(), ['chunkSizeBytes' => 0]);
    }

    /**
     * @dataProvider provideInputDataAndExpectedChunks
     */
    public function testDelete($input, $expectedChunks): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream($input));

        $this->assertCollectionCount($this->filesCollection, 1);
        $this->assertCollectionCount($this->chunksCollection, $expectedChunks);

        $this->bucket->delete($id);

        $this->assertCollectionCount($this->filesCollection, 0);
        $this->assertCollectionCount($this->chunksCollection, 0);
    }

    public function provideInputDataAndExpectedChunks()
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

    public function testDeleteShouldRequireFileToExist(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->bucket->delete('nonexistent-id');
    }

    /**
     * @dataProvider provideInputDataAndExpectedChunks
     */
    public function testDeleteStillRemovesChunksIfFileDoesNotExist($input, $expectedChunks): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream($input));

        $this->assertCollectionCount($this->filesCollection, 1);
        $this->assertCollectionCount($this->chunksCollection, $expectedChunks);

        $this->filesCollection->deleteOne(['_id' => $id]);

        try {
            $this->bucket->delete($id);
            $this->fail('FileNotFoundException was not thrown');
        } catch (FileNotFoundException $e) {
        }

        $this->assertCollectionCount($this->chunksCollection, 0);
    }

    public function testDownloadingFileWithMissingChunk(): void
    {
        $id = $this->bucket->uploadFromStream("filename", $this->createStream("foobar"));

        $this->chunksCollection->deleteOne(['files_id' => $id, 'n' => 0]);

        $this->expectException(CorruptFileException::class);
        $this->expectExceptionMessage('Chunk not found for index "0"');
        stream_get_contents($this->bucket->openDownloadStream($id));
    }

    public function testDownloadingFileWithUnexpectedChunkIndex(): void
    {
        $id = $this->bucket->uploadFromStream("filename", $this->createStream("foobar"));

        $this->chunksCollection->updateOne(
            ['files_id' => $id, 'n' => 0],
            ['$set' => ['n' => 1]]
        );

        $this->expectException(CorruptFileException::class);
        $this->expectExceptionMessage('Expected chunk to have index "0" but found "1"');
        stream_get_contents($this->bucket->openDownloadStream($id));
    }

    public function testDownloadingFileWithUnexpectedChunkSize(): void
    {
        $id = $this->bucket->uploadFromStream("filename", $this->createStream("foobar"));

        $this->chunksCollection->updateOne(
            ['files_id' => $id, 'n' => 0],
            ['$set' => ['data' => new Binary('fooba', Binary::TYPE_GENERIC)]]
        );

        $this->expectException(CorruptFileException::class);
        $this->expectExceptionMessage('Expected chunk to have size "6" but found "5"');
        stream_get_contents($this->bucket->openDownloadStream($id));
    }

    /**
     * @dataProvider provideInputDataAndExpectedChunks
     */
    public function testDownloadToStream($input): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream($input));
        $destination = $this->createStream();
        $this->bucket->downloadToStream($id, $destination);

        $this->assertStreamContents($input, $destination);
    }

    /**
     * @dataProvider provideInvalidStreamValues
     */
    public function testDownloadToStreamShouldRequireDestinationStream($destination): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bucket->downloadToStream('id', $destination);
    }

    public function provideInvalidStreamValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidStreamValues());
    }

    public function testDownloadToStreamShouldRequireFileToExist(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->bucket->downloadToStream('nonexistent-id', $this->createStream());
    }

    public function testDownloadToStreamByName(): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));
        $this->bucket->uploadFromStream('filename', $this->createStream('bar'));
        $this->bucket->uploadFromStream('filename', $this->createStream('baz'));

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination);
        $this->assertStreamContents('baz', $destination);

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination, ['revision' => -3]);
        $this->assertStreamContents('foo', $destination);

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination, ['revision' => -2]);
        $this->assertStreamContents('bar', $destination);

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination, ['revision' => -1]);
        $this->assertStreamContents('baz', $destination);

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination, ['revision' => 0]);
        $this->assertStreamContents('foo', $destination);

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination, ['revision' => 1]);
        $this->assertStreamContents('bar', $destination);

        $destination = $this->createStream();
        $this->bucket->downloadToStreamByName('filename', $destination, ['revision' => 2]);
        $this->assertStreamContents('baz', $destination);
    }

    /**
     * @dataProvider provideInvalidStreamValues
     */
    public function testDownloadToStreamByNameShouldRequireDestinationStream($destination): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bucket->downloadToStreamByName('filename', $destination);
    }

    /**
     * @dataProvider provideNonexistentFilenameAndRevision
     */
    public function testDownloadToStreamByNameShouldRequireFilenameAndRevisionToExist($filename, $revision): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));
        $this->bucket->uploadFromStream('filename', $this->createStream('bar'));

        $destination = $this->createStream();
        $this->expectException(FileNotFoundException::class);
        $this->bucket->downloadToStreamByName($filename, $destination, ['revision' => $revision]);
    }

    public function provideNonexistentFilenameAndRevision()
    {
        return [
            ['filename', 2],
            ['filename', -3],
            ['nonexistent-filename', 0],
            ['nonexistent-filename', -1],
        ];
    }

    public function testDrop(): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foobar'));

        $this->assertCollectionCount($this->filesCollection, 1);
        $this->assertCollectionCount($this->chunksCollection, 1);

        $this->bucket->drop();

        $this->assertCollectionDoesNotExist($this->filesCollection->getCollectionName());
        $this->assertCollectionDoesNotExist($this->chunksCollection->getCollectionName());
    }

    public function testFind(): void
    {
        $this->bucket->uploadFromStream('a', $this->createStream('foo'));
        $this->bucket->uploadFromStream('b', $this->createStream('foobar'));
        $this->bucket->uploadFromStream('c', $this->createStream('foobarbaz'));

        $cursor = $this->bucket->find(
            ['length' => ['$lte' => 6]],
            [
                'projection' => [
                    'filename' => 1,
                    'length' => 1,
                    '_id' => 0,
                ],
                'sort' => ['length' => -1],
            ]
        );

        $expected = [
            ['filename' => 'b', 'length' => 6],
            ['filename' => 'a', 'length' => 3],
        ];

        $this->assertSameDocuments($expected, $cursor);
    }

    public function testFindUsesTypeMap(): void
    {
        $this->bucket->uploadFromStream('a', $this->createStream('foo'));

        $cursor = $this->bucket->find();
        $fileDocument = current($cursor->toArray());

        $this->assertInstanceOf(BSONDocument::class, $fileDocument);
    }

    public function testFindOne(): void
    {
        $this->bucket->uploadFromStream('a', $this->createStream('foo'));
        $this->bucket->uploadFromStream('b', $this->createStream('foobar'));
        $this->bucket->uploadFromStream('c', $this->createStream('foobarbaz'));

        $fileDocument = $this->bucket->findOne(
            ['length' => ['$lte' => 6]],
            [
                'projection' => [
                    'filename' => 1,
                    'length' => 1,
                    '_id' => 0,
                ],
                'sort' => ['length' => -1],
            ]
        );

        $this->assertInstanceOf(BSONDocument::class, $fileDocument);
        $this->assertSameDocument(['filename' => 'b', 'length' => 6], $fileDocument);
    }

    public function testGetBucketNameWithCustomValue(): void
    {
        $bucket = new Bucket($this->manager, $this->getDatabaseName(), ['bucketName' => 'custom_fs']);

        $this->assertEquals('custom_fs', $bucket->getBucketName());
    }

    public function testGetBucketNameWithDefaultValue(): void
    {
        $this->assertEquals('fs', $this->bucket->getBucketName());
    }

    public function testGetChunksCollection(): void
    {
        $chunksCollection = $this->bucket->getChunksCollection();

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
        $this->assertEquals(261120, $this->bucket->getChunkSizeBytes());
    }

    public function testGetDatabaseName(): void
    {
        $this->assertEquals($this->getDatabaseName(), $this->bucket->getDatabaseName());
    }

    public function testGetFileDocumentForStreamUsesTypeMap(): void
    {
        $metadata = ['foo' => 'bar'];
        $stream = $this->bucket->openUploadStream('filename', ['_id' => 1, 'metadata' => $metadata]);

        $fileDocument = $this->bucket->getFileDocumentForStream($stream);

        $this->assertInstanceOf(BSONDocument::class, $fileDocument);
        $this->assertInstanceOf(BSONDocument::class, $fileDocument['metadata']);
        $this->assertSame(['foo' => 'bar'], $fileDocument['metadata']->getArrayCopy());
    }

    public function testGetFileDocumentForStreamWithReadableStream(): void
    {
        $metadata = ['foo' => 'bar'];
        $id = $this->bucket->uploadFromStream('filename', $this->createStream('foobar'), ['metadata' => $metadata]);
        $stream = $this->bucket->openDownloadStream($id);

        $fileDocument = $this->bucket->getFileDocumentForStream($stream);

        $this->assertSameObjectId($id, $fileDocument->_id);
        $this->assertSame('filename', $fileDocument->filename);
        $this->assertSame(6, $fileDocument->length);
        $this->assertSameDocument($metadata, $fileDocument->metadata);
    }

    public function testGetFileDocumentForStreamWithWritableStream(): void
    {
        $metadata = ['foo' => 'bar'];
        $stream = $this->bucket->openUploadStream('filename', ['_id' => 1, 'metadata' => $metadata]);

        $fileDocument = $this->bucket->getFileDocumentForStream($stream);

        $this->assertEquals(1, $fileDocument->_id);
        $this->assertSame('filename', $fileDocument->filename);
        $this->assertSameDocument($metadata, $fileDocument->metadata);
    }

    /**
     * @dataProvider provideInvalidGridFSStreamValues
     */
    public function testGetFileDocumentForStreamShouldRequireGridFSStreamResource($stream): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bucket->getFileDocumentForStream($stream);
    }

    public function provideInvalidGridFSStreamValues()
    {
        return $this->wrapValuesForDataProvider(array_merge($this->getInvalidStreamValues(), [$this->createStream()]));
    }

    public function testGetFileIdForStreamUsesTypeMap(): void
    {
        $stream = $this->bucket->openUploadStream('filename', ['_id' => ['x' => 1]]);

        $id = $this->bucket->getFileIdForStream($stream);

        $this->assertInstanceOf(BSONDocument::class, $id);
        $this->assertSame(['x' => 1], $id->getArrayCopy());
    }

    public function testGetFileIdForStreamWithReadableStream(): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream('foobar'));
        $stream = $this->bucket->openDownloadStream($id);

        $this->assertSameObjectId($id, $this->bucket->getFileIdForStream($stream));
    }

    public function testGetFileIdForStreamWithWritableStream(): void
    {
        $stream = $this->bucket->openUploadStream('filename', ['_id' => 1]);

        $this->assertEquals(1, $this->bucket->getFileIdForStream($stream));
    }

    /**
     * @dataProvider provideInvalidGridFSStreamValues
     */
    public function testGetFileIdForStreamShouldRequireGridFSStreamResource($stream): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bucket->getFileIdForStream($stream);
    }

    public function testGetFilesCollection(): void
    {
        $filesCollection = $this->bucket->getFilesCollection();

        $this->assertInstanceOf(Collection::class, $filesCollection);
        $this->assertEquals('fs.files', $filesCollection->getCollectionName());
    }

    /**
     * @dataProvider provideInputDataAndExpectedChunks
     */
    public function testOpenDownloadStream($input): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream($input));

        $this->assertStreamContents($input, $this->bucket->openDownloadStream($id));
    }

    /**
     * @dataProvider provideInputDataAndExpectedChunks
     */
    public function testOpenDownloadStreamAndMultipleReadOperations($input): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream($input));
        $stream = $this->bucket->openDownloadStream($id);
        $buffer = '';

        while (strlen($buffer) < strlen($input)) {
            $expectedReadLength = min(4096, strlen($input) - strlen($buffer));
            $buffer .= $read = fread($stream, 4096);

            $this->assertIsString($read);
            $this->assertEquals($expectedReadLength, strlen($read));
        }

        $this->assertTrue(fclose($stream));
        $this->assertEquals($input, $buffer);
    }

    public function testOpenDownloadStreamShouldRequireFileToExist(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->bucket->openDownloadStream('nonexistent-id');
    }

    public function testOpenDownloadStreamByNameShouldRequireFilenameToExist(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->bucket->openDownloadStream('nonexistent-filename');
    }

    public function testOpenDownloadStreamByName(): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));
        $this->bucket->uploadFromStream('filename', $this->createStream('bar'));
        $this->bucket->uploadFromStream('filename', $this->createStream('baz'));

        $this->assertStreamContents('baz', $this->bucket->openDownloadStreamByName('filename'));
        $this->assertStreamContents('foo', $this->bucket->openDownloadStreamByName('filename', ['revision' => -3]));
        $this->assertStreamContents('bar', $this->bucket->openDownloadStreamByName('filename', ['revision' => -2]));
        $this->assertStreamContents('baz', $this->bucket->openDownloadStreamByName('filename', ['revision' => -1]));
        $this->assertStreamContents('foo', $this->bucket->openDownloadStreamByName('filename', ['revision' => 0]));
        $this->assertStreamContents('bar', $this->bucket->openDownloadStreamByName('filename', ['revision' => 1]));
        $this->assertStreamContents('baz', $this->bucket->openDownloadStreamByName('filename', ['revision' => 2]));
    }

    /**
     * @dataProvider provideNonexistentFilenameAndRevision
     */
    public function testOpenDownloadStreamByNameShouldRequireFilenameAndRevisionToExist($filename, $revision): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));
        $this->bucket->uploadFromStream('filename', $this->createStream('bar'));

        $this->expectException(FileNotFoundException::class);
        $this->bucket->openDownloadStream($filename, ['revision' => $revision]);
    }

    public function testOpenUploadStream(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        fwrite($stream, 'foobar');
        fclose($stream);

        $this->assertStreamContents('foobar', $this->bucket->openDownloadStreamByName('filename'));
    }

    /**
     * @dataProvider provideInputDataAndExpectedChunks
     */
    public function testOpenUploadStreamAndMultipleWriteOperations($input): void
    {
        $stream = $this->bucket->openUploadStream('filename');
        $offset = 0;

        while ($offset < strlen($input)) {
            $expectedWriteLength = min(4096, strlen($input) - $offset);
            $writeLength = fwrite($stream, substr($input, $offset, 4096));
            $offset += $writeLength;

            $this->assertEquals($expectedWriteLength, $writeLength);
        }

        $this->assertTrue(fclose($stream));
        $this->assertStreamContents($input, $this->bucket->openDownloadStreamByName('filename'));
    }

    public function testRename(): void
    {
        $id = $this->bucket->uploadFromStream('a', $this->createStream('foo'));
        $this->bucket->rename($id, 'b');

        $fileDocument = $this->filesCollection->findOne(
            ['_id' => $id],
            ['projection' => ['filename' => 1, '_id' => 0]]
        );

        $this->assertSameDocument(['filename' => 'b'], $fileDocument);
        $this->assertStreamContents('foo', $this->bucket->openDownloadStreamByName('b'));
    }

    public function testRenameShouldNotRequireFileToBeModified(): void
    {
        $id = $this->bucket->uploadFromStream('a', $this->createStream('foo'));
        $this->bucket->rename($id, 'a');

        $fileDocument = $this->filesCollection->findOne(
            ['_id' => $id],
            ['projection' => ['filename' => 1, '_id' => 0]]
        );

        $this->assertSameDocument(['filename' => 'a'], $fileDocument);
        $this->assertStreamContents('foo', $this->bucket->openDownloadStreamByName('a'));
    }

    public function testRenameShouldRequireFileToExist(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->bucket->rename('nonexistent-id', 'b');
    }

    public function testUploadFromStream(): void
    {
        $options = [
            '_id' => 'custom-id',
            'chunkSizeBytes' => 2,
            'metadata' => ['foo' => 'bar'],
        ];

        $id = $this->bucket->uploadFromStream('filename', $this->createStream('foobar'), $options);

        $this->assertCollectionCount($this->filesCollection, 1);
        $this->assertCollectionCount($this->chunksCollection, 3);
        $this->assertSame('custom-id', $id);

        $fileDocument = $this->filesCollection->findOne(['_id' => $id]);

        $this->assertSameDocument(['foo' => 'bar'], $fileDocument['metadata']);
    }

    /**
     * @dataProvider provideInvalidStreamValues
     */
    public function testUploadFromStreamShouldRequireSourceStream($source): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bucket->uploadFromStream('filename', $source);
    }

    public function testUploadingAnEmptyFile(): void
    {
        $id = $this->bucket->uploadFromStream('filename', $this->createStream(''));
        $destination = $this->createStream();
        $this->bucket->downloadToStream($id, $destination);

        $this->assertStreamContents('', $destination);
        $this->assertCollectionCount($this->filesCollection, 1);
        $this->assertCollectionCount($this->chunksCollection, 0);

        $fileDocument = $this->filesCollection->findOne(
            ['_id' => $id],
            [
                'projection' => [
                    'length' => 1,
                    'md5' => 1,
                    '_id' => 0,
                ],
            ]
        );

        $expected = [
            'length' => 0,
            'md5' => 'd41d8cd98f00b204e9800998ecf8427e',
        ];

        $this->assertSameDocument($expected, $fileDocument);
    }

    public function testUploadingFirstFileCreatesIndexes(): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));

        $this->assertIndexExists($this->filesCollection->getCollectionName(), 'filename_1_uploadDate_1');
        $this->assertIndexExists($this->chunksCollection->getCollectionName(), 'files_id_1_n_1', function (IndexInfo $info): void {
            $this->assertTrue($info->isUnique());
        });
    }

    public function testExistingIndexIsReused(): void
    {
        $this->filesCollection->createIndex(['filename' => 1.0, 'uploadDate' => 1], ['name' => 'test']);
        $this->chunksCollection->createIndex(['files_id' => 1.0, 'n' => 1], ['name' => 'test', 'unique' => true]);

        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));

        $this->assertIndexNotExists($this->filesCollection->getCollectionName(), 'filename_1_uploadDate_1');
        $this->assertIndexNotExists($this->chunksCollection->getCollectionName(), 'files_id_1_n_1');
    }

    public function testDownloadToStreamFails(): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'), ['_id' => ['foo' => 'bar']]);

        $this->expectException(StreamException::class);
        $this->expectExceptionMessageMatches('#^Downloading file from "gridfs://.*/.*/.*" to "php://temp" failed. GridFS identifier: "{ "_id" : { "foo" : "bar" } }"$#');
        $this->bucket->downloadToStream(['foo' => 'bar'], fopen('php://temp', 'r'));
    }

    public function testDownloadToStreamByNameFails(): void
    {
        $this->bucket->uploadFromStream('filename', $this->createStream('foo'));

        $this->expectException(StreamException::class);
        $this->expectExceptionMessageMatches('#^Downloading file from "gridfs://.*/.*/.*" to "php://temp" failed. GridFS filename: "filename"$#');
        $this->bucket->downloadToStreamByName('filename', fopen('php://temp', 'r'));
    }

    public function testUploadFromStreamFails(): void
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped('Test only works on PHP 7.4 and newer');
        }

        UnusableStream::register();
        $source = fopen('unusable://temp', 'w');

        $this->expectException(StreamException::class);
        $this->expectExceptionMessageMatches('#^Uploading file from "unusable://temp" to "gridfs://.*/.*/.*" failed. GridFS filename: "filename"$#');
        $this->bucket->uploadFromStream('filename', $source);
    }

    public function testDanglingOpenWritableStream(): void
    {
        if (! strncasecmp(PHP_OS, 'WIN', 3)) {
            $this->markTestSkipped('Test does not apply to Windows');
        }

        $path = __DIR__ . '/../../vendor/autoload.php';
        $command = <<<CMD
php -r "require '$path'; \\\$stream = (new MongoDB\Client)->test->selectGridFSBucket()->openUploadStream('filename', ['disableMD5' => true]);" 2>&1
CMD;

        @exec(
            $command,
            $output,
            $return
        );

        $this->assertSame(0, $return);
        $output = implode(PHP_EOL, $output);

        $this->assertSame('', $output);
    }

    /**
     * Asserts that an index with the given name exists for the collection.
     *
     * An optional $callback may be provided, which should take an IndexInfo
     * argument as its first and only parameter. If an IndexInfo matching the
     * given name is found, it will be passed to the callback, which may perform
     * additional assertions.
     *
     * @param string   $collectionName
     * @param string   $indexName
     * @param callable $callback
     */
    private function assertIndexExists(string $collectionName, string $indexName, ?callable $callback = null): void
    {
        if ($callback !== null && ! is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $operation = new ListIndexes($this->getDatabaseName(), $collectionName);
        $indexes = $operation->execute($this->getPrimaryServer());

        $foundIndex = null;

        foreach ($indexes as $index) {
            if ($index->getName() === $indexName) {
                $foundIndex = $index;
                break;
            }
        }

        $this->assertNotNull($foundIndex, sprintf('Index %s does not exist', $indexName));

        if ($callback !== null) {
            call_user_func($callback, $foundIndex);
        }
    }

    /**
     * Asserts that an index with the given name does not exist for the collection.
     *
     * @param string $collectionName
     * @param string $indexName
     */
    private function assertIndexNotExists(string $collectionName, string $indexName): void
    {
        $operation = new ListIndexes($this->getDatabaseName(), $collectionName);
        $indexes = $operation->execute($this->getPrimaryServer());

        $foundIndex = false;

        foreach ($indexes as $index) {
            if ($index->getName() === $indexName) {
                $foundIndex = true;
                break;
            }
        }

        $this->assertFalse($foundIndex, sprintf('Index %s exists', $indexName));
    }

    /**
     * Return a list of invalid stream values.
     *
     * @return array
     */
    private function getInvalidStreamValues(): array
    {
        return [null, 123, 'foo', [], hash_init('md5')];
    }
}
