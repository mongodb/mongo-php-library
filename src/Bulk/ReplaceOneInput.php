<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;

/**
 * Class ReplaceOneInput
 * @package MongoDB\src\Bulk
 */
class ReplaceOneInput implements BulkInputInterface
{
    private $updateInput;
    
    public function __construct($filter, $replacement, array $options = [])
    {
        if ( ! is_array($replacement) && ! is_object($replacement)) {
            throw InvalidArgumentException::invalidType('$replacement', $replacement, 'array or object');
        }

        if (\MongoDB\is_first_key_operator($replacement)) {
            throw new InvalidArgumentException('First key in $replacement argument is an update operator');
        }
        
        $this->updateInput = new UpdateInput(
            $filter,
            $replacement,
            $options + ['upsert' => false]
        );
    }

    public function addToBulk(BulkWrite $bulk)
    {
        $this->updateInput->addToBulk($bulk);
    }
}