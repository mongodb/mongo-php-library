<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;

/**
 * Class InsertOneInput
 * @package MongoDB\Bulk
 */
class InsertOneInput implements BulkInputInterface
{
    /**
     * @var array|object
     */
    private $document;

    /**
     * @param array|object $document
     */
    public function __construct($document)
    {
        if ( ! is_array($document) && ! is_object($document)) {
            throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
        }
        
        $this->document = $document;
    }

    /**
     * @return array|object
     */
    public function getDocument()
    {
        return $this->document;
    }
    
    public function addToBulk(BulkWrite $bulk)
    {
        return $bulk->insert($this->document);
    }
}