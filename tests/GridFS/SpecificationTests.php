<?php

namespace MongoDB\Tests\GridFS;

use \MongoDB\GridFS;
use \MongoDB\Collection;
use \MongoDB\BSON\ObjectId;
use \MongoDB\BSON\Binary;

class SpecificationTests extends FunctionalTestCase
{
    private $commands;
    private $collections;


    public function setUp()
    {
        parent::setUp();
       $this->commands = array(
                        'insert' => function($col, $docs) {
                            $col->insertMany($docs['documents']);}
        );
    }
    /**
     *@dataProvider provideSpecificationTests
     */
    public function testSpecificationTests($testJson)
    {
        foreach ($testJson['tests'] as $test) {
            $this->bucket = new \MongoDB\GridFS\Bucket($this->manager, $this->getDatabaseName(), $test['act']['arguments']['options']);
            $this->bucketReadWriter = new \MongoDB\GridFS\BucketReadWriter($this->bucket);
            $func = $test['act']['operation'] . "Command";
            $error = null;
            try {
                $result = $this->$func($test['act']['arguments']);
            } catch(Exception $e) {
                $error = $e;
            }
            if (!isset($test['assert']['error'])) {
                //check that test didn't throw error
            } else {
                //check that the error matches what we got
            }
            if (isset($test['assert']['result'])) {
                $testResult = $test['assert']['result'];
                if ($testResult == "&result") {
                    $test['assert']['result'] = $result;
                }
                if ($testResult == "void") {
                    $test['assert']['result'] = null;
                }
                $this->assertEquals($result, $test['assert']['result']);
            }
            if (isset($test['assert']['data'])) {
                $this->runCommands($test['assert']['data'], $result);
                $this->collectionsEqual($this->collections['expected.files'],$this->bucket->getFilesCollection());
                if(isset($this->collections['expected.chunks'])) {
                    $this->collectionsEqual($this->collections['expected.chunks'],$this->bucket->getChunksCollection());
                }
            }
        }
    }

    public function provideSpecificationTests()
    {
        $testPath=getcwd().'/tests/GridFS/Specification/tests/upload.json';

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
                $result[$key] = new ObjectId("".$value['$oid']);
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
        $cmds = $this->fixTypes($cmds, true);
        foreach($cmds as $cmd) {
            foreach($cmd as $key => $value) {
                if(isset($this->commands[$key])) {
                    $cmdName = $key;
                    $collectionName = $value;

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
                    $collection = new Collection($this->manager, sprintf("%s.%s", $this->getDatabaseName(), $collectionName));
                    $this->commands[$key]($collection, $this->fixTypes($cmd, true));
                    $this->collections[$collectionName] = $collection;
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
        return $this->bucketReadWriter->uploadFromStream($args['filename'], $stream, $args['options']);
    }
    function downloadCommand($args)
    {

    }
    function deleteCommand($args)
    {

    }
    function download_by_nameCommand($args)
    {

    }
}
