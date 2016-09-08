<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\GridFS\CollectionWrapper;
use MongoDB\GridFS\WritableStream;

/**
 * Functional tests for the internal WritableStream class.
 */
class WritableStreamFunctionalTest extends FunctionalTestCase
{
    private $collectionWrapper;

    public function setUp()
    {
        parent::setUp();

        $this->collectionWrapper = new CollectionWrapper($this->manager, $this->getDatabaseName(), 'fs');
    }

    public function testValidConstructorOptions()
    {
        new WritableStream($this->collectionWrapper, 'filename', [
            '_id' => 'custom-id',
            'chunkSizeBytes' => 2,
            'metadata' => ['foo' => 'bar'],
        ]);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new WritableStream($this->collectionWrapper, 'filename', $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['chunkSizeBytes' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['metadata' => $value];
        }

        return $options;
    }

    /**
     * @dataProvider provideInputDataAndExpectedMD5
     */
    public function testInsertChunksCalculatesMD5($input, $expectedMD5)
    {
        $stream = new WritableStream($this->collectionWrapper, 'filename');
        $stream->insertChunks($input);
        $stream->close();

        $fileDocument = $this->filesCollection->findOne(
            ['_id' => $stream->getFile()->_id],
            ['projection' => ['md5' => 1, '_id' => 0]]
        );

        $this->assertSameDocument(['md5' => $expectedMD5], $fileDocument);
    }

    public function provideInputDataAndExpectedMD5()
    {
        return [
            ['', 'd41d8cd98f00b204e9800998ecf8427e'],
            ['foobar', '3858f62230ac3c915f300c664312c63f'],
            [str_repeat('foobar', 43520), '88ff0e5fcb0acb27947d736b5d69cb73'],
            [str_repeat('foobar', 43521), '8ff86511c95a06a611842ceb555d8454'],
            [str_repeat('foobar', 87040), '45bfa1a9ec36728ee7338d15c5a30c13'],
            [str_repeat('foobar', 87041), '95e78f624f8e745bcfd2d11691fa601e'],
        ];
    }
}
