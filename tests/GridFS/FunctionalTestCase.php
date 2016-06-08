<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\Collection;
use MongoDB\GridFS\Bucket;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for GridFS functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    protected $bucket;
    protected $chunksCollection;
    protected $filesCollection;

    public function setUp()
    {
        parent::setUp();

        $this->bucket = new Bucket($this->manager, $this->getDatabaseName());
        $this->bucket->drop();

        $this->chunksCollection = new Collection($this->manager, $this->getDatabaseName(), 'fs.chunks');
        $this->filesCollection = new Collection($this->manager, $this->getDatabaseName(), 'fs.files');
    }

    /**
     * Rewinds a stream and asserts its contents.
     *
     * @param string   $expectedContents
     * @param resource $stream
     */
    protected function assertStreamContents($expectedContents, $stream)
    {
        $this->assertEquals($expectedContents, stream_get_contents($stream, -1,.0));
    }

    /**
     * Creates an in-memory stream with the given data.
     *
     * @param string $data
     * @return resource
     */
    protected function createStream($data = '')
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $data);
        rewind($stream);

        return $stream;
    }

    public function provideInsertChunks()
    {
        $dataVals = [];
        $testArgs[][] = "hello world";
        $testArgs[][] = "1234567890";
        $testArgs[][] = "~!@#$%^&*()_+";
        for($j=0; $j<30; $j++){
            $randomTest = "";
            for($i=0; $i<100; $i++){
                $randomTest .= chr(rand(0, 256));
            }
            $testArgs[][] = $randomTest;
        }
        $utf8="";
        for($i=0; $i<256; $i++){
            $utf8 .= chr($i);
        }
        $testArgs[][]=$utf8;
        return $testArgs;
    }

}
