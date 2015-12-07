<?php
namespace MongoDB\GridFS;

use MongoDB\Collection;
use MongoDB\Exception\RuntimeException;
/**
 * GridFsStream holds the configuration for upload or download streams to GridFS
 *
 * @api
 */
class GridFsStream
{
    protected $bucket;
    protected $n;
    protected $buffer;
    protected $file;
    /**
    * Constructs a GridFsStream
    *
    * @param \MongoDB\GridFS\Bucket  $bucket   GridFS Bucket
    */
    public function __construct(Bucket $bucket)
    {
        $this->bucket = $bucket;
        $this->n = 0;
        $this->buffer = fopen('php://temp', 'w+');
    }
}
