<?php

namespace MongoDB\Operation;

use MongoDB\InsertOneResult;
use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentTypeException;

/**
 * Operation for inserting a single document with the insert command.
 *
 * @api
 * @see MongoDB\Collection::insertOne()
 * @see http://docs.mongodb.org/manual/reference/command/insert/
 */
class InsertOne implements Executable
{
    private $databaseName;
    private $collectionName;
    private $document;
    private $options;

    /**
     * Constructs an insert command.
     *
     * Supported options:
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $document       Document to insert
     * @param array        $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, $document, array $options = [])
    {
        if ( ! is_array($document) && ! is_object($document)) {
            throw new InvalidArgumentTypeException('$document', $document, 'array or object');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw new InvalidArgumentTypeException('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->document = $document;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return InsertOneResult
     */
    public function execute(Server $server)
    {
        $bulk = new Bulk();
        $insertedId = $bulk->insert($this->document);

        if ($insertedId === null) {
            // TODO: This may be removed if PHPC-382 is implemented
            $insertedId = is_array($this->document) ? $this->document['_id'] : $this->document->_id;
        }

        $writeConcern = isset($this->options['writeConcern']) ? $this->options['writeConcern'] : null;
        $writeResult = $server->executeBulkWrite($this->databaseName . '.' . $this->collectionName, $bulk, $writeConcern);

        return new InsertOneResult($writeResult, $insertedId);
    }
}
