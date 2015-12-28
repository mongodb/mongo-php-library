<?php

namespace MongoDB\Tests\GridFS;

use \MongoDB\GridFS;
use \MongoDB\Collection;
use \MongoDB\BSON\ObjectId;
use \MongoDB\BSON\Binary;
use \MongoDB\Exception;

class SpecificationTests extends FunctionalTestCase
{
    private $commands;
    private $collections;

    public function setUp()
    {
        parent::setUp();
       $this->commands = array(
                        'insert' => function($col, $docs) {
                            $col->insertMany($docs['documents']);},
                        'update' => function($col, $docs) {
                            foreach($docs['updates'] as $update) {
                                $col->updateMany($update['q'], $update['u']);
                            }
                        },
                        'delete' => function($col, $docs){
                            foreach($docs['deletes'] as $delete){
                                $col->deleteMany($delete['q']);
                            }
                        }
        );
    }
    /**
     *@dataProvider provideSpecificationTests
     */
    public function testSpecificationTests($testJson)
    {
        foreach ($testJson['tests'] as $test) {
            $this->initializeDatabases($testJson['data'], $test);

            if(isset($test['act']['arguments']['options'])){
                $options = $test['act']['arguments']['options'];
            } else {
                $options =[];
            }
            $this->bucket = new \MongoDB\GridFS\Bucket($this->manager, $this->getDatabaseName(), $this->fixTypes($options,false));
            $func = $test['act']['operation'] . "Command";
            $error = null;
            try {
                $result = $this->$func($test['act']['arguments']);
            } catch(\MongoDB\Exception\Exception $e) {
                $error = $e;
            }
            $errors = ['FileNotFound' =>  '\MongoDB\Exception\GridFSFileNotFoundException',
                        'ChunkIsMissing' => '\MongoDB\Exception\GridFSCorruptFileException',
                        'ExtraChunk' => '\MongoDB\Exception\GridFSCorruptFileException',
                        'ChunkIsWrongSize' => '\MongoDB\Exception\GridFSCorruptFileException',
                        'RevisionNotFound' => '\MongoDB\Exception\GridFSFileNotFoundException'
                ];
            if (!isset($test['assert']['error'])) {
                $this->assertNull($error);
            } else {
                $shouldError = $test['assert']['error'];
                $this->assertTrue($error instanceof $errors[$shouldError]);
            }
            if (isset($test['assert']['result'])) {
                    $testResult = $test['assert']['result'];
                if ($testResult == '&result') {
                    $test['assert']['result'] = $result;
                }
                if ($testResult == "void") {
                    $test['assert']['result'] = null;
                }
                $fixedAssertFalse = $this->fixTypes($test['assert'], false);
                $this->assertEquals($result, $fixedAssertFalse['result']);
            }
            $fixedAssertTrue = $this->fixTypes($test['assert'], true);
            if (isset($test['assert']['data'])) {
                $this->runCommands($fixedAssertTrue['data'], $result);
                $this->collectionsEqual($this->collections['expected.files'],$this->bucket->getCollectionsWrapper()->getFilesCollection());
                if(isset($this->collections['expected.chunks'])) {
                    $this->collectionsEqual($this->collections['expected.chunks'],$this->bucket->getCollectionsWrapper()->getChunksCollection());
                }
            }
        }
    }

    public function provideSpecificationTests()
    {
        $testPath= __DIR__.'/Specification/tests/*.json';
        $testArgs = [];
        foreach(glob($testPath) as $filename) {
            $fileContents = file_get_contents($filename);
            $testJson = json_decode($fileContents, true);
            $testArgs[][] = $testJson;
        }
        return $testArgs;
    }

    public function fixTypes($testJson, $makeBinary)
    {
        $result = $testJson;
        foreach($result as $key =>$value) {
            if (is_array($value) && isset($value['$hex'])) {
                $result[$key] = hex2bin($value['$hex']);
                if($makeBinary) {
                    $result[$key] = new \MongoDB\BSON\Binary($result[$key], \MongoDB\BSON\Binary::TYPE_GENERIC);
                }
            } else if (is_array($value) && isset($value['$oid'])) {
                $result[$key] = new \MongoDB\BSON\ObjectId("".$value['$oid']);
            } else if (is_array($value)) {
                $result[$key] = $this->fixTypes($result[$key], $makeBinary);
            } else if(is_string($value) && $value == '*actual') {
                unset($result[$key]);
            }
        }
        return $result;
    }

    public function collectionsEqual($col1, $col2)
    {
        $docs1 = $this->filterDoc($col1, true);
        $docs2 = $this->filterDoc($col2, true);
        $this->assertSameDocuments($docs1, $docs2);
    }

    public function filterDoc($collection, $ignoreId)
    {
        $output = [];
        $documents = $collection->find();
        foreach($documents as $doc){
            if ($ignoreId) {
                unset($doc->_id);
            }
            if(isset($doc->uploadDate)) {
           //     $this->assertTrue($doc->uploadDate instanceof DateTime);
                unset($doc->uploadDate);
            }
            $output [] = $doc;
        }
        return $output;
    }

    public function runCommands($cmds, $result)
    {
        foreach($cmds as $cmd){
            foreach($cmd as $key => $value) {
                if(isset($this->commands[$key])) {
                    $cmdName = $key;
                    $collectionName = $value;
                    if(isset($cmd['documents'])){
                        foreach($cmd['documents'] as $docIndex => $doc) {
                            foreach($doc as $docKey => $docVal){
                                if(is_string($docVal)) {
                                    if($docVal == '*result') {
                                        $doc[$docKey] = $result;
                                    }
                                }
                            }
                            $cmd['documents'][$docIndex] = $doc;
                        }
                    }
                    $collection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), $collectionName));
                    $this->commands[$key]($collection, $this->fixTypes($cmd, true));
                    $this->collections[$collectionName] = $collection;
                }
            }
        }

    }

    public function initializeDatabases($data, $test)
    {
        $collectionsToDrop = ['fs.files','fs.chunks','expected.files','expected.chunks'];
        $data = $this->fixTypes($data, true);
        foreach ($collectionsToDrop as $collectionName) {
            $collection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), $collectionName));
            $collection->drop();
        }
        if (isset($data['files']) && count($data['files']) > 0) {
            $filesCollection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), "fs.files"));
            $filesCollection->insertMany($data['files']);
            $expectedFilesCollection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), "expected.files"));
            $expectedFilesCollection->insertMany($data['files']);
            $this->collections['expected.files'] = $expectedFilesCollection;
        }
        if (isset($data['chunks']) && count($data['chunks']) > 0) {
            $chunksCollection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), "fs.chunks"));
            $chunksCollection->insertMany($data['chunks']);
            $expectedChunksCollection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), "expected.chunks"));
            $expectedChunksCollection->insertMany($data['chunks']);
            $this->collections['expected.chunks'] = $expectedChunksCollection;

        }
        if(isset($test['arrange'])) {
            foreach($test['arrange']['data'] as $cmd) {
                foreach($cmd as $key => $value) {
                    if(isset($this->commands[$key])) {
                        $collection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), $cmd[$key]));
                        $this->commands[$key]($collection,$this->fixTypes($cmd, true));
                    }
                }
            }
        }
    }
    public function uploadCommand($args)
    {
        $args = $this->fixTypes($args, false);
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $args['source']);
        rewind($stream);
        $result = $this->bucket->uploadFromStream($args['filename'], $stream, $args['options']);
        fclose($stream);
        return $result;
    }
    function downloadCommand($args)
    {
        $args = $this->fixTypes($args, false);
        $streamWrapper = new \MongoDB\GridFS\StreamWrapper();
        $streamWrapper->register($this->manager);
        $stream = fopen('php://temp', 'w+');
        $this->bucket->downloadToStream($args['id'], $stream);
        rewind($stream);
        $result = stream_get_contents($stream);
        fclose($stream);
        return $result;
    }
    function deleteCommand($args)
    {
        $args = $this->fixTypes($args, false);
        $this->bucket->delete($args['id']);
    }
    function download_by_nameCommand($args)
    {
        $args = $this->fixTypes($args, false);
        $streamWrapper = new \MongoDB\GridFS\StreamWrapper();
        $streamWrapper->register($this->manager);
        $stream = fopen('php://temp', 'w+');
        if(isset($args['options']['revision'])) {
            $this->bucket->downloadToStreamByName($args['filename'], $stream, $args['options']['revision']);
        } else {
            $this->bucket->downloadToStreamByName($args['filename'], $stream);
        }
        rewind($stream);
        $result = stream_get_contents($stream);
        fclose($stream);
        return $result;

    }
}
