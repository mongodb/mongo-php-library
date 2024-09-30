<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\Collection;
use MongoDB\GridFS\Bucket;
use MongoDB\Operation\DropCollection;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BeforeClass;

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
    protected Bucket $bucket;

    protected Collection $chunksCollection;

    protected Collection $filesCollection;

    public function setUp(): void
    {
        parent::setUp();

        $this->bucket = new Bucket($this->manager, $this->getDatabaseName());

        $this->chunksCollection = new Collection($this->manager, $this->getDatabaseName(), 'fs.chunks');
        $this->filesCollection = new Collection($this->manager, $this->getDatabaseName(), 'fs.files');
    }

    public function tearDown(): void
    {
        $this->chunksCollection->deleteMany([]);
        $this->filesCollection->deleteMany([]);

        parent::tearDown();
    }

    /**
     * The bucket's collections are created by the first test that runs and
     * kept for all subsequent tests. This is done to avoid creating the
     * collections and their indexes for each test, which would be slow.
     */
    #[BeforeClass]
    #[AfterClass]
    public static function dropCollectionsBeforeAfterClass(): void
    {
        $manager = static::createTestManager();

        (new DropCollection(self::getDatabaseName(), 'fs.chunks'))->execute($manager->selectServer());
        (new DropCollection(self::getDatabaseName(), 'fs.files'))->execute($manager->selectServer());
    }

    /**
     * Asserts that a variable is a stream containing the expected data.
     *
     * Note: this will seek to the beginning of the stream before reading.
     *
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
     * @return resource
     */
    protected static function createStream(string $data = '')
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $data);
        rewind($stream);

        return $stream;
    }
}
