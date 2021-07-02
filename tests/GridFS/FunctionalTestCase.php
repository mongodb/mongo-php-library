<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\Collection;
use MongoDB\GridFS\Bucket;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

use function fopen;
use function fwrite;
use function get_resource_type;
use function rewind;
use function stream_get_contents;

/**
 * Base class for GridFS functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    /** @var Bucket */
    protected $bucket;

    /** @var Collection */
    protected $chunksCollection;

    /** @var Collection */
    protected $filesCollection;

    public function setUp(): void
    {
        parent::setUp();

        $this->bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->bucket->drop();

        $this->chunksCollection = new Collection($this->manager, $this->getDatabaseName(), 'fs.chunks');
        $this->filesCollection = new Collection($this->manager, $this->getDatabaseName(), 'fs.files');
    }

    /**
     * Asserts that a variable is a stream containing the expected data.
     *
     * Note: this will seek to the beginning of the stream before reading.
     *
     * @param string   $expectedContents
     * @param resource $stream
     */
    protected function assertStreamContents(string $expectedContents, $stream): void
    {
        $this->assertIsResource($stream);
        $this->assertSame('stream', get_resource_type($stream));
        $this->assertEquals($expectedContents, stream_get_contents($stream, -1, 0));
    }

    /**
     * Creates an in-memory stream with the given data.
     *
     * @param string $data
     * @return resource
     */
    protected function createStream(string $data = '')
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $data);
        rewind($stream);

        return $stream;
    }
}
