<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;

class UpdateManyInput implements BulkInputInterface
{
    private $updateInput;

    /**
     * UpdateManyInput constructor.
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     */
    public function __construct($filter, $update, array $options = [])
    {
        if ( ! is_array($update) && ! is_object($update)) {
            throw InvalidArgumentException::invalidType('$update', $update, 'array or object');
        }

        if ( ! \MongoDB\is_first_key_operator($update)) {
            throw new InvalidArgumentException('First key in $update argument is not an update operator');
        }
        
        $this->updateInput = new UpdateInput(
            $filter,
            $update,
            ['multi' => true] + $options
        );
    }

    public function addToBulk(BulkWrite $bulk)
    {
        $this->updateInput->addToBulk($bulk);
    }
}