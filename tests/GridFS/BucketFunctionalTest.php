<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\GridFS;

/**
 * Functional tests for the Bucket class.
 */
class BucketFunctionalTest extends FunctionalTestCase
{

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
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

    public function testGetDatabaseName()
    {
        $this->assertEquals($this->getDatabaseName(), $this->bucket->getDatabaseName());
    }

    public function testBasicOperations()
    {
        $id = $this->bucket->uploadFromStream("test_filename", $this->generateStream("hello world"));
        $contents = stream_get_contents($this->bucket->openDownloadStream($id));
        $this->assertEquals("hello world", $contents);
        $this->assertEquals(1, $this->bucket->getCollectionsWrapper()->getFilesCollection()->count());
        $this->assertEquals(1, $this->bucket->getCollectionsWrapper()->getChunksCollection()->count());

        $this->bucket->delete($id);
        $error=null;
        try{
            $this->bucket->openDownloadStream($id);
        } catch(\MongoDB\Exception\Exception $e) {
            $error = $e;
        }
        $fileNotFound = '\MongoDB\Exception\GridFSFileNotFoundException';
        $this->assertTrue($error instanceof $fileNotFound);
        $this->assertEquals(0, $this->bucket->getCollectionsWrapper()->getFilesCollection()->count());
        $this->assertEquals(0, $this->bucket->getCollectionsWrapper()->getChunksCollection()->count());
    }
    public function testMultiChunkDelete()
    {
        $id = $this->bucket->uploadFromStream("test_filename", $this->generateStream("hello"), ['chunkSizeBytes'=>1]);
        $this->assertEquals(1, $this->bucket->getCollectionsWrapper()->getFilesCollection()->count());
        $this->assertEquals(5, $this->bucket->getCollectionsWrapper()->getChunksCollection()->count());
        $this->bucket->delete($id);
        $this->assertEquals(0, $this->bucket->getCollectionsWrapper()->getFilesCollection()->count());
        $this->assertEquals(0, $this->bucket->getCollectionsWrapper()->getChunksCollection()->count());
    }

    public function testEmptyFile()
    {
        $id = $this->bucket->uploadFromStream("test_filename",$this->generateStream(""));
        $contents = stream_get_contents($this->bucket->openDownloadStream($id));
        $this->assertEquals("", $contents);
        $this->assertEquals(1, $this->bucket->getCollectionsWrapper()->getFilesCollection()->count());
        $this->assertEquals(0, $this->bucket->getCollectionsWrapper()->getChunksCollection()->count());

        $raw = $this->bucket->getCollectionsWrapper()->getFilesCollection()->findOne();
        $this->assertEquals(0, $raw->length);
        $this->assertEquals($id, $raw->_id);
        $this->assertTrue($raw->uploadDate instanceof \MongoDB\BSON\UTCDateTime);
        $this->assertEquals(255 * 1024, $raw->chunkSize);
        $this->assertTrue(is_string($raw->md5));
    }
    public function testUploadEnsureIndexes()
    {
        $chunks = $this->bucket->getCollectionsWrapper()->getChunksCollection();
        $files = $this->bucket->getCollectionsWrapper()->getFilesCollection();
        $this->bucket->uploadFromStream("filename", $this->generateStream("junk"));

        $chunksIndexed = false;
        foreach($chunks->listIndexes() as $index) {
            $chunksIndexed = $chunksIndexed || ($index->isUnique() && $index->getKey() === ['files_id' => 1, 'n' => 1]);
        }
        $this->assertTrue($chunksIndexed);

        $filesIndexed = false;
        foreach($files->listIndexes() as $index) {
            $filesIndexed = $filesIndexed || ($index->getKey() === ['filename' => 1, 'uploadDate' => 1]);
        }
        $this->assertTrue($filesIndexed);
    }
    public function testGetLastVersion()
    {
        $idOne = $this->bucket->uploadFromStream("test",$this->generateStream("foo"));
        $streamTwo = $this->bucket->openUploadStream("test");
        fwrite($streamTwo, "bar");
        //echo "Calling FSTAT\n";
        //$stat = fstat($streamTwo);
        $idTwo = $this->bucket->getIdFromStream($streamTwo);
        //var_dump
        //var_dump($idTwo);
        fclose($streamTwo);

        $idThree = $this->bucket->uploadFromStream("test",$this->generateStream("baz"));
        $this->assertEquals("baz", stream_get_contents($this->bucket->openDownloadStreamByName("test")));
        $this->bucket->delete($idThree);
        $this->assertEquals("bar", stream_get_contents($this->bucket->openDownloadStreamByName("test")));
        $this->bucket->delete($idTwo);
        $this->assertEquals("foo", stream_get_contents($this->bucket->openDownloadStreamByName("test")));
        $this->bucket->delete($idOne);
        $error = null;
        try{
            $this->bucket->openDownloadStreamByName("test");
        } catch(\MongoDB\Exception\Exception $e) {
            $error = $e;
        }
        $fileNotFound = '\MongoDB\Exception\GridFSFileNotFoundException';
        $this->assertTrue($error instanceof $fileNotFound);
    }
    public function testGetVersion()
    {
        $this->bucket->uploadFromStream("test",$this->generateStream("foo"));
        $this->bucket->uploadFromStream("test",$this->generateStream("bar"));
        $this->bucket->uploadFromStream("test",$this->generateStream("baz"));

        $this->assertEquals("foo", stream_get_contents($this->bucket->openDownloadStreamByName("test", 0)));
        $this->assertEquals("bar", stream_get_contents($this->bucket->openDownloadStreamByName("test", 1)));
        $this->assertEquals("baz", stream_get_contents($this->bucket->openDownloadStreamByName("test", 2)));

        $this->assertEquals("baz", stream_get_contents($this->bucket->openDownloadStreamByName("test", -1)));
        $this->assertEquals("bar", stream_get_contents($this->bucket->openDownloadStreamByName("test", -2)));
        $this->assertEquals("foo", stream_get_contents($this->bucket->openDownloadStreamByName("test", -3)));

        $fileNotFound = '\MongoDB\Exception\GridFSFileNotFoundException';
        $error = null;
        try{
            $this->bucket->openDownloadStreamByName("test", 3);
        } catch(\MongoDB\Exception\Exception $e) {
            $error = $e;
        }
        $this->assertTrue($error instanceof $fileNotFound);
        $error = null;
        try{
            $this->bucket->openDownloadStreamByName("test", -4);
        } catch(\MongoDB\Exception\Exception $e) {
            $error = $e;
        }
        $this->assertTrue($error instanceof $fileNotFound);
    }
    public function testGridfsFind()
    {
        $this->bucket->uploadFromStream("two",$this->generateStream("test2"));
        usleep(5000);
        $this->bucket->uploadFromStream("two",$this->generateStream("test2+"));
        usleep(5000);
        $this->bucket->uploadFromStream("one",$this->generateStream("test1"));
        usleep(5000);
        $this->bucket->uploadFromStream("two",$this->generateStream("test2++"));
        $cursor = $this->bucket->find(["filename" => "two"]);
        $count = count($cursor->toArray());
        $this->assertEquals(3, $count);
        $cursor = $this->bucket->find([]);
        $count = count($cursor->toArray());
        $this->assertEquals(4, $count);

        $cursor = $this->bucket->find([], ["noCursorTimeout"=>false, "sort"=>["uploadDate"=> -1], "skip"=>1, "limit"=>2]);
        $outputs = ["test1", "test2+"];
        $i=0;
        foreach($cursor as $file){
            $contents = stream_get_contents($this->bucket->openDownloadStream($file->_id));
            $this->assertEquals($outputs[$i], $contents);
            $i++;
        }
    }
    public function testGridInNonIntChunksize()
    {
        $id = $this->bucket->uploadFromStream("f",$this->generateStream("data"));
        $this->bucket->getCollectionsWrapper()->getFilesCollection()->updateOne(["filename"=>"f"],
                                                        ['$set'=> ['chunkSize' => 100.00]]);
        $this->assertEquals("data", stream_get_contents($this->bucket->openDownloadStream($id)));
    }
    public function testBigInsert()
    {
        $testPath= __DIR__."/BigInsertTest.txt";
        $testStream = fopen($testPath, "r");
        $id = $this->bucket->uploadFromStream("BigInsertTest", $testStream);
    }
    public function testGetIdFromStream()
    {
        $upload = $this->bucket->openUploadStream("test");
        $id = $this->bucket->getIdFromStream($upload);
        fclose($upload);
        $this->assertTrue($id instanceof \MongoDB\BSON\ObjectId);

        $download = $this->bucket->openDownloadStream($id);
        $id=null;
        $id = $this->bucket->getIdFromStream($download);
        fclose($download);
        $this->assertTrue($id instanceof \MongoDB\BSON\ObjectId);
    }
    /**
     *@dataProvider provideInsertChunks
     */
    public function testProvidedMultipleReads($data)
    {
        $upload = $this->bucket->openUploadStream("test", ["chunkSizeBytes"=>rand(1, 5)]);
        fwrite($upload,$data);
        $id = $this->bucket->getIdFromStream($upload);
        fclose($upload);
        $download = $this->bucket->openDownloadStream($id);
        $readPos = 0;
        while($readPos < strlen($data)){
            $numToRead = rand(1, strlen($data) - $readPos);
            $expected = substr($data, $readPos, $numToRead);
            $actual = fread($download, $numToRead);
            $this->assertEquals($expected,$actual);
            $readPos+= $numToRead;
        }
        $actual = fread($download, 5);
        $expected = "";
        $this->assertEquals($expected,$actual);
        fclose($download);
    }
    private function generateStream($input)
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $input);
        rewind($stream);
        return $stream;
    }
}
