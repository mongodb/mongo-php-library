<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\GridFS;

/**
 * Functional tests for the Bucket class.
 */
class GridFsStreamTest extends FunctionalTestCase
{

/*    public function testConstructorOptionTypeChecks(array $options)
    {
        new \MongoDB\GridFS\Bucket($this->manager, $this->getDatabaseName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];
        $invalidBucketNames = [123, 3.14, true, [], new \stdClass];
        $invalidChunkSizes = ['foo', 3.14, true, [], new \stdClass];


        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }
        foreach ($invalidBucketNames as $value) {
            $options[][] = ['bucketName' => $value];
        }
        foreach ($invalidChunkSizes as $value) {
            $options[][] = ['chunkSizeBytes' => $value];
        }

        return $options;
    }
*/
    public function testBasic()
    {
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test");
        $upload->insertChunks("hello world");
        $id = $upload->getId();
        $upload->close();

        $this->assertEquals(1, $this->collectionsWrapper->getFilesCollection()->count());
        $this->assertEquals(1, $this->collectionsWrapper->getChunksCollection()->count());

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$id]);

        $download = new \MongoDB\GridFS\GridFsDownload($this->collectionsWrapper, $file);
        $stream = fopen('php://temp', 'w+');
        $download->downloadToStream($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        $this->assertEquals("hello world", $contents);
        fclose($stream);

        #make sure it's still there!
        $download = new \MongoDB\GridFS\GridFsDownload($this->collectionsWrapper, $file);
        $stream = fopen('php://temp', 'w+');
        $download->downloadToStream($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        $this->assertEquals("hello world", $contents);
        fclose($stream);

        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test");
        $id = $upload->getId();
        $upload->close();

        $this->assertEquals(2, $this->collectionsWrapper->getFilesCollection()->count());
        $this->assertEquals(1, $this->collectionsWrapper->getChunksCollection()->count());

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$id]);
        $download = new \MongoDB\GridFS\GridFsDownload($this->collectionsWrapper, $file);
        $stream = fopen('php://temp', 'w+');
        $download->downloadToStream($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);

        $this->assertEquals("", $contents);
    }

    public function testMd5()
    {
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test");
        $upload->insertChunks("hello world\n");
        $id = $upload->getId();
        $upload->close();

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$id]);
        $this->assertEquals("6f5902ac237024bdd0c176cb93063dc4", $file->md5);
    }
    public function testUploadDefaultOpts()
    {
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test");

        $this->assertTrue($upload->getId() instanceof \MongoDB\BSON\ObjectId);
        $this->assertTrue($upload->getFile()["uploadDate"] instanceof \MongoDB\BSON\UTCDateTime);

        $this->assertEquals($upload->getFile()["filename"], "test");
        $this->assertEquals($upload->getLength(),0);

        $this->assertTrue(!isset($upload->getFile()["contentType"]));
        $this->assertTrue(!isset($upload->getFile()["aliases"]));
        $this->assertTrue(!isset($upload->getFile()["metadata"]));

        $this->assertEquals(255 * 1024, $upload->getChunkSize());
    }
    public function testUploadCustomOpts()
    {
        $options = ["chunkSizeBytes" => 1,
                 "contentType" => "text/html",
                 "aliases" => ["foo", "bar"],
                 "metadata" => ["foo" => 1, "bar" => 2]
                 ];
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test", $options);
        $this->assertEquals($upload->getChunkSize(), 1);
        $this->assertEquals($upload->getFile()["contentType"], "text/html");
        $this->assertEquals($upload->getFile()["aliases"], ["foo", "bar"]);
        $this->assertEquals($upload->getFile()["metadata"], ["foo" => 1, "bar" => 2]);
    }
    public function testDownloadDefaultOpts()
    {
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test");
        $upload->close();

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id" => $upload->getId()]);
        $download = new \MongoDB\GridFS\GridFsDownload($this->collectionsWrapper, $file);
        $download->close();

        $this->assertEquals($upload->getId(), $download->getId());
        $this->assertEquals(0, $download->getFile()->length);
        $this->assertTrue(!isset($download->getFile()->contentType));
        $this->assertTrue(!isset($download->getFile()->aliases));
        $this->assertTrue(!isset($download->getFile()->metadata));
        $this->assertTrue($download->getFile()->uploadDate instanceof \MongoDB\BSON\UTCDateTime);
        $this->assertEquals(255 * 1024, $download->getFile()->chunkSize);
        $this->assertEquals("d41d8cd98f00b204e9800998ecf8427e", $download->getFile()->md5);
    }
    public function testDownloadCustomOpts()
    {
        $options = ["chunkSizeBytes" => 1000,
                 "contentType" => "text/html",
                 "aliases" => ["foo", "bar"],
                 "metadata" => ["foo" => 1, "bar" => 2]
                 ];
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test", $options);
        $upload->insertChunks("hello world");
        $upload->close();

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id" => $upload->getId()]);
        $download = new \MongoDB\GridFS\GridFsDownload($this->collectionsWrapper, $file);

        $this->assertEquals("test", $download->getFile()->filename);
        $this->assertEquals($upload->getId(), $download->getId());
        $this->assertEquals(11, $download->getFile()->length);
        $this->assertEquals("text/html", $download->getFile()->contentType);
        $this->assertEquals(1000, $download->getFile()->chunkSize);
        $this->assertEquals(["foo", "bar"], $download->getFile()->aliases);
        $this->assertEquals(["foo"=> 1, "bar"=> 2], (array) $download->getFile()->metadata);
        $this->assertEquals("5eb63bbbe01eeed093cb22bb8f5acdc3", $download->getFile()->md5);
    }
    /**
     *@dataProvider provideInsertChunks
     */
    public function testInsertChunks($data)
    {
        $upload = new \MongoDB\GridFS\GridFsUpload($this->collectionsWrapper, "test");
        $upload->insertChunks($data);
        $upload->close();
        $stream = $this->bucket->openDownloadStream($upload->getId());
        $this->assertEquals($data, stream_get_contents($stream));
    }

    public function provideInsertChunks()
    {
        $dataVals = [];
        $testArgs[][] = "hello world";
        $testArgs[][] = "1234567890";
        $testArgs[][] = "~!@#$%^&*()_+";
        for($j=0; $j<10; $j++){
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
    private function generateStream($input)
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $input);
        rewind($stream);
        return $stream;
    }
}
