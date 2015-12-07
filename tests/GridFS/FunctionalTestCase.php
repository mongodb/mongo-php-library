<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\GridFS;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for GridFS functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    protected $bucket;
    protected $bucketReadWriter;

    public function setUp()
    {
        parent::setUp();
        $streamWrapper = new \MongoDB\GridFS\StreamWrapper();
        $streamWrapper->register($this->manager);
        $this->bucket = new \MongoDB\GridFS\Bucket($this->manager, $this->getDatabaseName());
        $this->bucketReadWriter = new \MongoDB\GridFS\BucketReadWriter($this->bucket);
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            return;
        }
        //$this->database = new \MongoDB\Database($this->manager, $this->getDatabaseName());
     //   $this->database->drop();
    }
}
