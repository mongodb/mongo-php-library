<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\GridFS;

/**
 * Functional tests for the Bucket class.
 */
class GridFSStreamTest extends FunctionalTestCase
{

    public function testBasic()
    {
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");
        $upload->insertChunks("hello world");
        $id = $upload->getId();
        $upload->close();

        $this->assertEquals(1, $this->collectionsWrapper->getFilesCollection()->count());
        $this->assertEquals(1, $this->collectionsWrapper->getChunksCollection()->count());

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$id], ['typeMap' => ['root' => 'stdClass']]);

        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);
        $stream = fopen('php://temp', 'w+');
        $download->downloadToStream($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        $this->assertEquals("hello world", $contents);
        fclose($stream);

        #make sure it's still there!
        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);
        $stream = fopen('php://temp', 'w+');
        $download->downloadToStream($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        $this->assertEquals("hello world", $contents);
        fclose($stream);

        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");
        $id = $upload->getId();
        $upload->close();

        $this->assertEquals(2, $this->collectionsWrapper->getFilesCollection()->count());
        $this->assertEquals(1, $this->collectionsWrapper->getChunksCollection()->count());

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$id], ['typeMap' => ['root' => 'stdClass']]);
        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);
        $stream = fopen('php://temp', 'w+');
        $download->downloadToStream($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);

        $this->assertEquals("", $contents);
    }

    public function testMd5()
    {
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");
        $upload->insertChunks("hello world\n");
        $id = $upload->getId();
        $upload->close();

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$id]);
        $this->assertEquals("6f5902ac237024bdd0c176cb93063dc4", $file->md5);
    }
    public function testUploadDefaultOpts()
    {
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");

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
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test", $options);
        $this->assertEquals($upload->getChunkSize(), 1);
        $this->assertEquals($upload->getFile()["contentType"], "text/html");
        $this->assertEquals($upload->getFile()["aliases"], ["foo", "bar"]);
        $this->assertEquals($upload->getFile()["metadata"], ["foo" => 1, "bar" => 2]);
    }
    public function testDownloadDefaultOpts()
    {
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");
        $upload->close();

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id" => $upload->getId()], ['typeMap' => ['root' => 'stdClass']]);
        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);
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
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test", $options);
        $upload->insertChunks("hello world");
        $upload->close();

        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id" => $upload->getId()], ['typeMap' => ['root' => 'stdClass']]);
        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);

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
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");
        $upload->insertChunks($data);
        $upload->close();
        $stream = $this->bucket->openDownloadStream($upload->getId());
        $this->assertEquals($data, stream_get_contents($stream));
    }

    public function testMultiChunkFile()
    {
        $toUpload="";
        for($i=0; $i<255*1024+1000; $i++){
            $toUpload .= "a";
        }
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test");
        $upload->insertChunks($toUpload);
        $upload->close();

        $this->assertEquals(1, $this->collectionsWrapper->getFilesCollection()->count());
        $this->assertEquals(2, $this->collectionsWrapper->getChunksCollection()->count());

        $download = $this->bucket->openDownloadStream($upload->getId());
        $this->assertEquals($toUpload, stream_get_contents($download));
    }
    /**
     *@dataProvider provideInsertChunks
     */
    public function testSmallChunks($data)
    {
        $options = ["chunkSizeBytes"=>1];
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test", $options);
        $upload->insertChunks($data);
        $upload->close();

        $this->assertEquals(strlen($data), $this->collectionsWrapper->getChunksCollection()->count());
        $this->assertEquals(1, $this->collectionsWrapper->getFilesCollection()->count());

        $stream = $this->bucket->openDownloadStream($upload->getId());
        $this->assertEquals($data, stream_get_contents($stream));
    }
    public function testMultipleReads()
    {
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test", ["chunkSizeBytes"=>3]);
        $upload->insertChunks("hello world");
        $upload->close();
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$upload->getId()], ['typeMap' => ['root' => 'stdClass']]);
        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);
        $this->assertEquals("he", $download->downloadNumBytes(2));
        $this->assertEquals("ll", $download->downloadNumBytes(2));
        $this->assertEquals("o ", $download->downloadNumBytes(2));
        $this->assertEquals("wo", $download->downloadNumBytes(2));
        $this->assertEquals("rl", $download->downloadNumBytes(2));
        $this->assertEquals("d", $download->downloadNumBytes(2));
        $this->assertEquals("", $download->downloadNumBytes(2));
        $this->assertEquals("", $download->downloadNumBytes(2));
        $download->close();
    }
    /**
     *@dataProvider provideInsertChunks
     */
    public function testProvidedMultipleReads($data)
    {
        $upload = new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper, "test", ["chunkSizeBytes"=>rand(1, 5)]);
        $upload->insertChunks($data);
        $upload->close();
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(["_id"=>$upload->getId()], ['typeMap' => ['root' => 'stdClass']]);
        $download = new \MongoDB\GridFS\GridFSDownload($this->collectionsWrapper, $file);

        $readPos = 0;
        while($readPos < strlen($data)){
            $numToRead = rand(1, strlen($data) - $readPos);
            $expected = substr($data, $readPos, $numToRead);
            $actual = $download->downloadNumBytes($numToRead);
            $this->assertEquals($expected,$actual);
            $readPos+= $numToRead;
        }
        $actual = $download->downloadNumBytes(5);
        $expected = "";
        $this->assertEquals($expected,$actual);
        $download->close();
    }
    /**
     * @expectedException \MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidUploadConstructorOptions
     */
    public function testUploadConstructorOptionTypeChecks(array $options)
    {
        new \MongoDB\GridFS\GridFSUpload($this->collectionsWrapper,"test", $options);
    }

    public function provideInvalidUploadConstructorOptions()
    {
        $options = [];
        $invalidContentType = [123, 3.14, true, [], new \stdClass];
        $invalidAliases = ['foo', 3.14, true, [12, 34], new \stdClass];
        $invalidMetadata = ['foo', 3.14, true];

        foreach ($invalidContentType as $value) {
            $options[][] = ['contentType' => $value];
        }
        foreach ($invalidAliases as $value) {
            $options[][] = ['aliases' => $value];
        }
        foreach ($invalidMetadata as $value) {
            $options[][] = ['metadata' => $value];
        }
        return $options;
    }
}
