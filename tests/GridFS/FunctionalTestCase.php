<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\GridFS;
use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for GridFS functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    protected $bucket;
    protected $collectionsWrapper;

    public function setUp()
    {
        parent::setUp();
       foreach(['fs.files', 'fs.chunks'] as $collection){
            $col = new Collection($this->manager, $this->getDatabaseName(), $collection);
            $col->drop();
        }
        $this->bucket = new \MongoDB\GridFS\Bucket($this->manager, $this->getDatabaseName());
        $this->collectionsWrapper = $this->bucket->getCollectionsWrapper();
    }

    public function tearDown()
    {
       foreach(['fs.files', 'fs.chunks'] as $collection){
            $col = new Collection($this->manager, $this->getDatabaseName(), $collection);
            $col->drop();
        }
        if ($this->hasFailed()) {
            return;
        }
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
