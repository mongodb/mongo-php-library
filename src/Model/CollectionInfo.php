<?php

namespace MongoDB\Model;

class CollectionInfo
{
    private $name;
    private $options;

    /**
    * Constructor.
    *
    * @param array $info Collection info
    */
    public function __construct(array $info)
    {
        $this->name = (string) $info['name'];
        $this->options = isset($info['options']) ? (array) $info['options'] : array();
    }

    /**
     * Return the collection name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the collection options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Return whether the collection is a capped collection.
     *
     * @return boolean
     */
    public function isCapped()
    {
        return isset($this->options['capped']) ? (boolean) $this->options['capped'] : false;
    }

    /**
     * Return the maximum number of documents to keep in the capped collection.
     *
     * @return integer|null
     */
    public function getCappedMax()
    {
        return isset($this->options['max']) ? (integer) $this->options['max'] : null;
    }

    /**
     * Return the maximum size (in bytes) of the capped collection.
     *
     * @return integer|null
     */
    public function getCappedSize()
    {
        return isset($this->options['size']) ? (integer) $this->options['size'] : null;
    }
}
