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
            $col = new Collection($this->manager, sprintf("%s.%s",$this->getDatabaseName(), $collection));
            $col->drop();
        }
        $streamWrapper = new \MongoDB\GridFS\StreamWrapper();
        $streamWrapper->register($this->manager);
        $this->bucket = new \MongoDB\GridFS\Bucket($this->manager, $this->getDatabaseName());
        $this->collectionsWrapper = $this->bucket->getCollectionsWrapper();
    }

    public function tearDown()
    {
       foreach(['fs.files', 'fs.chunks'] as $collection){
            $col = new Collection($this->manager, sprintf("%s.%s",$this->getDatabaseName(), $collection));
            $col->drop();
        }
        if ($this->hasFailed()) {
            return;
        }
    }
}
