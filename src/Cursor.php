<?php

namespace MongoDB;

use MongoDB\Operation\Find;

/**
 * Created for code compatibility layer to the legacy mongodb driver (only for Find method)
 * Can be useful for people who wants to migrate from legacy driver to the new one
 * Added "count", "limit", "skip", "sort" methods
 * Added the ability to use it as an iterator (e.g. in foreach clause)
 * @package MongoDB
 * @see legacy cursor class here http://php.net/manual/tr/class.mongocursor.php
 * @author Arda Beyazoglu
 */
class Cursor implements \IteratorAggregate {

    /**
     * @var \MongoDb\Driver\Cursor;
     */
    private $_originalCursor = null;

    /**
     * @var Collection
     */
    private $_collection;

    /**
     * @var array
     */
    private $_filter;

    /**
     * @var array
     */
    private $_options;

    /**
     * Cursor constructor.
     * @param Collection $collection
     * @param array $filter
     * @param array $options
     */
    public function __construct(Collection &$collection, array $filter = [], array $options = [])
    {
        $this->_collection = $collection;
        $this->_filter = $filter;
        $this->_options = $options;
    }

    /**
     * @return Driver\Cursor
     */
    public function getOriginalCursor(){
        if(null === $this->_originalCursor){
            $operation = new Find($this->_collection->getDatabaseName(), $this->_collection->getCollectionName(), $this->_filter, $this->_options);
            $server = $this->_collection->getManager()->selectServer($this->_options['readPreference']);
            $this->_originalCursor = $operation->execute($server);
        }

        return $this->_originalCursor;
    }

    /**
     * get iterator of the cursor
     * @return Driver\Cursor
     */
    public function getIterator(){
        return $this->getOriginalCursor();
    }

    /**
     * count number of records in the cursor
     * @return int
     */
    public function count(){
        return $this->_collection->count($this->_filter, $this->_options);
    }

    /**
     * sort cursor by $sort field
     * @param $sort
     * @return $this
     */
    public function sort($sort){
        $this->_options["sort"] = $sort;
        return $this;
    }

    /**
     * limit cursor by $limit
     * @param $limit
     * @return $this
     */
    public function limit($limit){
        $this->_options["limit"] = $limit;
        return $this;
    }

    /**
     * skip $skip records
     * @param $skip
     * @return $this
     */
    public function skip($skip){
        $this->_options["skip"] = $skip;
        return $this;
    }

    /** original cursor methods */

    /**
     * @return Driver\CursorId
     */
    public final function getId(){
        return $this->getOriginalCursor()->getId();
    }

    /**
     * @return Driver\Server
     */
    public final function getServer(){
        return $this->getOriginalCursor()->getServer();
    }

    /**
     * @return bool
     */
    public final function isDead(){
        return $this->getOriginalCursor()->isDead();
    }

    /**
     * @param array $typeMap
     */
    public final function setTypeMap(array $typeMap){
        $this->getOriginalCursor()->setTypeMap($typeMap);
    }

    /**
     * @return array
     */
    public final function toArray(){
        return $this->getOriginalCursor()->toArray();
    }
}

?>