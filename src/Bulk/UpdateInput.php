<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;

class UpdateInput implements BulkInputInterface
{
    /**
     * @var array|object
     */
    private $filter;

    /**
     * @var array|object
     */
    private $update;

    /**
     * @var array
     */
    private $options;

    /**
     * UpdateInput constructor.
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     */
    public function __construct($filter, $update, array $options = [])
    {
        if ( ! is_array($filter) && ! is_object($filter)) {
            throw InvalidArgumentException::invalidType('$filter', $filter, 'array or object');
        }

        if ( ! is_array($update) && ! is_object($update)) {
            throw InvalidArgumentException::invalidType('$update', $filter, 'array or object');
        }

        $options += [
            'multi' => false,
            'upsert' => false,
        ];

        if ( ! is_bool($options['multi'])) {
            throw InvalidArgumentException::invalidType('"multi" option', $options['multi'], 'boolean');
        }

        if ($options['multi'] && ! \MongoDB\is_first_key_operator($update)) {
            throw new InvalidArgumentException('"multi" option cannot be true if $update is a replacement document');
        }

        if ( ! is_bool($options['upsert'])) {
            throw InvalidArgumentException::invalidType('"upsert" option', $options['upsert'], 'boolean');
        }
        
        $this->filter = $filter;
        $this->update = $update;
        $this->options = $options;
    }
    
    public function addToBulk(BulkWrite $bulk)
    {
        $bulk->update($this->filter, $this->update, $this->options);
    }
}