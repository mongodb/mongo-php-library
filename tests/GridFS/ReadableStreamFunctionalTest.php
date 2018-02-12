<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\BSON\Binary;
use MongoDB\GridFS\CollectionWrapper;
use MongoDB\GridFS\ReadableStream;
use MongoDB\Tests\CommandObserver;
use stdClass;

/**
 * Functional tests for the internal ReadableStream class.
 */
class ReadableStreamFunctionalTest extends FunctionalTestCase
{
    private $collectionWrapper;

    public function setUp()
    {
        parent::setUp();

        $this->collectionWrapper = new CollectionWrapper($this->manager, $this->getDatabaseName(), 'fs');

        $this->filesCollection->insertMany([
            ['_id' => 'length-0', 'length' => 0, 'chunkSize' => 4],
            ['_id' => 'length-0-with-empty-chunk', 'length' => 0, 'chunkSize' => 4],
            ['_id' => 'length-2', 'length' => 2, 'chunkSize' => 4],
            ['_id' => 'length-8', 'length' => 8, 'chunkSize' => 4],
            ['_id' => 'length-10', 'length' => 10, 'chunkSize' => 4],
        ]);

        $this->chunksCollection->insertMany([
            ['_id' => 1, 'files_id' => 'length-0-with-empty-chunk', 'n' => 0, 'data' => new Binary('', Binary::TYPE_GENERIC)],
            ['_id' => 2, 'files_id' => 'length-2', 'n' => 0, 'data' => new Binary('ab', Binary::TYPE_GENERIC)],
            ['_id' => 3, 'files_id' => 'length-8', 'n' => 0, 'data' => new Binary('abcd', Binary::TYPE_GENERIC)],
            ['_id' => 4, 'files_id' => 'length-8', 'n' => 1, 'data' => new Binary('efgh', Binary::TYPE_GENERIC)],
            ['_id' => 5, 'files_id' => 'length-10', 'n' => 0, 'data' => new Binary('abcd', Binary::TYPE_GENERIC)],
            ['_id' => 6, 'files_id' => 'length-10', 'n' => 1, 'data' => new Binary('efgh', Binary::TYPE_GENERIC)],
            ['_id' => 7, 'files_id' => 'length-10', 'n' => 2, 'data' => new Binary('ij', Binary::TYPE_GENERIC)],
        ]);
    }

    public function testGetFile()
    {
        $fileDocument = (object) ['_id' => null, 'chunkSize' => 1, 'length' => 0];
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);
        $this->assertSame($fileDocument, $stream->getFile());
    }

    /**
     * @expectedException MongoDB\GridFS\Exception\CorruptFileException
     * @dataProvider provideInvalidConstructorFileDocuments
     */
    public function testConstructorFileDocumentChecks($file)
    {
        new ReadableStream($this->collectionWrapper, $file);
    }

    public function provideInvalidConstructorFileDocuments()
    {
        $options = [];

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = (object) ['_id' => 1, 'chunkSize' => $value, 'length' => 0];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = (object) ['_id' => 1, 'chunkSize' => 1, 'length' => $value];
        }

        $options[][] = (object) ['_id' => 1, 'chunkSize' => 0, 'length' => 0];
        $options[][] = (object) ['_id' => 1, 'chunkSize' => 1, 'length' => -1];
        $options[][] = (object) ['chunkSize' => 1, 'length' => 0];

        return $options;
    }

    /**
     * @dataProvider provideFileIdAndExpectedBytes
     */
    public function testReadBytes($fileId, $length, $expectedBytes)
    {
        $fileDocument = $this->collectionWrapper->findFileById($fileId);
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $this->assertSame($expectedBytes, $stream->readBytes($length));
    }

    public function provideFileIdAndExpectedBytes()
    {
        return [
            ['length-0', 0, ''],
            ['length-0', 2, ''],
            ['length-0-with-empty-chunk', 0, ''],
            ['length-0-with-empty-chunk', 2, ''],
            ['length-2', 0, ''],
            ['length-2', 2, 'ab'],
            ['length-2', 4, 'ab'],
            ['length-8', 0, ''],
            ['length-8', 2, 'ab'],
            ['length-8', 4, 'abcd'],
            ['length-8', 6, 'abcdef'],
            ['length-8', 8, 'abcdefgh'],
            ['length-8', 10, 'abcdefgh'],
            ['length-10', 0, ''],
            ['length-10', 2, 'ab'],
            ['length-10', 4, 'abcd'],
            ['length-10', 6, 'abcdef'],
            ['length-10', 8, 'abcdefgh'],
            ['length-10', 10, 'abcdefghij'],
            ['length-10', 12, 'abcdefghij'],
        ];
    }

    public function provideFilteredFileIdAndExpectedBytes()
    {
        return array_filter($this->provideFileIdAndExpectedBytes(),
            function(array $args) {
                return $args[1] > 0;
            }
        );
    }

    /**
     * @dataProvider provideFilteredFileIdAndExpectedBytes
     */
    public function testReadBytesCalledMultipleTimes($fileId, $length, $expectedBytes)
    {
        $fileDocument = $this->collectionWrapper->findFileById($fileId);
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);
        for ($i = 0; $i < $length; $i++) {
            $expectedByte = isset($expectedBytes[$i]) ? $expectedBytes[$i] : '';
            $this->assertSame($expectedByte, $stream->readBytes(1));
        }
    }

    /**
     * @expectedException MongoDB\GridFS\Exception\CorruptFileException
     * @expectedExceptionMessage Chunk not found for index "2"
     */
    public function testReadBytesWithMissingChunk()
    {
        $this->chunksCollection->deleteOne(['files_id' => 'length-10', 'n' => 2]);

        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $stream->readBytes(10);
    }

    /**
     * @expectedException MongoDB\GridFS\Exception\CorruptFileException
     * @expectedExceptionMessage Expected chunk to have index "1" but found "2"
     */
    public function testReadBytesWithUnexpectedChunkIndex()
    {
        $this->chunksCollection->deleteOne(['files_id' => 'length-10', 'n' => 1]);

        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $stream->readBytes(10);
    }

    /**
     * @expectedException MongoDB\GridFS\Exception\CorruptFileException
     * @expectedExceptionMessage Expected chunk to have size "2" but found "1"
     */
    public function testReadBytesWithUnexpectedChunkSize()
    {
        $this->chunksCollection->updateOne(
            ['files_id' => 'length-10', 'n' => 2],
            ['$set' => ['data' => new Binary('i', Binary::TYPE_GENERIC)]]
        );

        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $stream->readBytes(10);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testReadBytesWithNegativeLength()
    {
        $fileDocument = $this->collectionWrapper->findFileById('length-0');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $stream->readBytes(-1);
    }

    public function testSeekBeforeReading()
    {
        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $stream->seek(8);
        $this->assertSame('ij', $stream->readBytes(2));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $offset must be >= 0 and <= 10; given: 11
     */
    public function testSeekOutOfRange()
    {
        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        $stream->seek(11);
    }

    /**
     * @dataProvider providePreviousChunkSeekOffsetAndBytes
     */
    public function testSeekPreviousChunk($offset, $length, $expectedBytes)
    {
        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        // Read to initialize and advance the chunk iterator to the last chunk
        $this->assertSame('abcdefghij', $stream->readBytes(10));

        $commands = [];

        (new CommandObserver)->observe(
            function() use ($stream, $offset, $length, $expectedBytes) {
                $stream->seek($offset);
                $this->assertSame($expectedBytes, $stream->readBytes($length));
            },
            function(stdClass $command) use (&$commands) {
                $commands[] = key((array) $command);
            }
        );

        $this->assertSame(['find'], $commands);
    }

    public function providePreviousChunkSeekOffsetAndBytes()
    {
        return [
            [0, 4, 'abcd'],
            [2, 4, 'cdef'],
            [4, 4, 'efgh'],
            [6, 4, 'ghij'],
        ];
    }

    /**
     * @dataProvider provideSameChunkSeekOffsetAndBytes
     */
    public function testSeekSameChunk($offset, $length, $expectedBytes)
    {
        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        // Read to initialize and advance the chunk iterator to the middle chunk
        $this->assertSame('abcdef', $stream->readBytes(6));

        $commands = [];

        (new CommandObserver)->observe(
            function() use ($stream, $offset, $length, $expectedBytes) {
                $stream->seek($offset);
                $this->assertSame($expectedBytes, $stream->readBytes($length));
            },
            function(stdClass $command) use (&$commands) {
                $commands[] = key((array) $command);
            }
        );

        $this->assertSame([], $commands);
    }

    public function provideSameChunkSeekOffsetAndBytes()
    {
        return [
            [4, 4, 'efgh'],
            [6, 4, 'ghij'],
        ];
    }

    /**
     * @dataProvider provideSubsequentChunkSeekOffsetAndBytes
     */
    public function testSeekSubsequentChunk($offset, $length, $expectedBytes)
    {
        $fileDocument = $this->collectionWrapper->findFileById('length-10');
        $stream = new ReadableStream($this->collectionWrapper, $fileDocument);

        // Read to initialize the chunk iterator to the first chunk
        $this->assertSame('a', $stream->readBytes(1));

        $commands = [];

        (new CommandObserver)->observe(
            function() use ($stream, $offset, $length, $expectedBytes) {
                $stream->seek($offset);
                $this->assertSame($expectedBytes, $stream->readBytes($length));
            },
            function(stdClass $command) use (&$commands) {
                $commands[] = key((array) $command);
            }
        );

        $this->assertSame([], $commands);
    }

    public function provideSubsequentChunkSeekOffsetAndBytes()
    {
        return [
            [4, 4, 'efgh'],
            [6, 4, 'ghij'],
            [8, 2, 'ij'],
        ];
    }
}
