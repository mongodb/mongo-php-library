<?php

namespace MongoDB\Model;

class DatabaseInfo
{
    private $empty;
    private $name;
    private $sizeOnDisk;

    /**
    * Constructor.
    *
    * @param array $info Database info
    */
    public function __construct(array $info)
    {
        $this->name = (string) $info['name'];
        $this->empty = (boolean) $info['empty'];
        $this->sizeOnDisk = (integer) $info['sizeOnDisk'];
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the databases size on disk (in bytes).
     *
     * @return integer
     */
    public function getSizeOnDisk()
    {
        return $this->sizeOnDisk;
    }

    /**
     * Return whether the database is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->empty;
    }
}
