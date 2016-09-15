<?php

namespace MongoDB\Operation;

use MongoDB\Bulk\InsertOneInput;
use MongoDB\BulkWriteResult;
use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Bulk\BulkInputInterface;

/**
 * Operation for executing multiple write operations.
 *
 * @api
 * @see \MongoDB\Collection::bulkWrite()
 */
class BulkWrite implements Executable
{
    const DELETE_MANY = 'deleteMany';
    const DELETE_ONE  = 'deleteOne';
    const INSERT_ONE  = 'insertOne';
    const REPLACE_ONE = 'replaceOne';
    const UPDATE_MANY = 'updateMany';
    const UPDATE_ONE  = 'updateOne';

    private static $wireVersionForDocumentLevelValidation = 4;
    private static $operationTypesMap = [
        self::DELETE_MANY => 'MongoDB\Bulk\DeleteManyInput',
        self::DELETE_ONE  => 'MongoDB\Bulk\DeleteOneInput',
        self::INSERT_ONE  => 'MongoDB\Bulk\InsertOneInput',
        self::REPLACE_ONE => 'MongoDB\Bulk\ReplaceOneInput',
        self::UPDATE_MANY => 'MongoDB\Bulk\UpdateManyInput',
        self::UPDATE_ONE  => 'MongoDB\Bulk\UpdateOneInput',
    ];

    private $databaseName;
    private $collectionName;

    /**
     * @var BulkInputInterface[]
     */
    private $operations;
    private $options;

    /**
     * Constructs a bulk write operation.
     *
     * Example array structure for all supported operation types:
     *
     *  [
     *    [ 'deleteMany' => [ $filter ] ],
     *    [ 'deleteOne'  => [ $filter ] ],
     *    [ 'insertOne'  => [ $document ] ],
     *    [ 'replaceOne' => [ $filter, $replacement, $options ] ],
     *    [ 'updateMany' => [ $filter, $update, $options ] ],
     *    [ 'updateOne'  => [ $filter, $update, $options ] ],
     *  ]
     *
     * Arguments correspond to the respective Operation classes; however, the
     * writeConcern option is specified for the top-level bulk write operation
     * instead of each individual operation.
     *
     * Supported options for replaceOne, updateMany, and updateOne operations:
     *
     *  * upsert (boolean): When true, a new document is created if no document
     *    matches the query. The default is false.
     *
     * Supported options for the bulk write operation:
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to opt
     *    out of document level validation.
     *
     *  * ordered (boolean): If true, when an insert fails, return without
     *    performing the remaining writes. If false, when a write fails,
     *    continue with the remaining writes, if any. The default is true.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string  $databaseName   Database name
     * @param string  $collectionName Collection name
     * @param array[] $operations     List of write operations
     * @param array   $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, array $operations, array $options = [])
    {
        if (empty($operations)) {
            throw new InvalidArgumentException('$operations is empty');
        }

        $expectedIndex = 0;

        foreach ($operations as $i => $operation) {
            if ($i !== $expectedIndex) {
                throw new InvalidArgumentException(sprintf('$operations is not a list (unexpected index: "%s")', $i));
            }
            
            if ($operation instanceof BulkInputInterface) {
                $expectedIndex += 1;
                continue;
            }

            if ( ! is_array($operation)) {
                throw InvalidArgumentException::invalidType(sprintf('$operations[%d]', $i), $operation, 'array or MongoDB\Bulk\BulkInputInterface instance');
            }

            if (count($operation) !== 1) {
                throw new InvalidArgumentException(sprintf('Expected one element in $operation[%d], actually: %d', $i, count($operation)));
            }

            $type = key($operation);
            $args = current($operation);


            if ( ! array_key_exists($type, self::$operationTypesMap)) {
                throw new InvalidArgumentException(sprintf('Unknown operation type "%s" in $operations[%d]', $type, $i));
            }

            $inputClass = self::$operationTypesMap[$type];
            $reflection = new \ReflectionClass($inputClass);
            try {
                $operations[$i] = $reflection->newInstanceArgs($args);
            } catch(InvalidArgumentException $e) {
                throw new InvalidArgumentException(sprintf(
                    'Exception during parsing "$operations[%d]": "%s"',
                    $i,
                    $e->getMessage()
                ));
            }

            
            $expectedIndex += 1;
        }

        $options += ['ordered' => true];

        if (isset($options['bypassDocumentValidation']) && ! is_bool($options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $options['bypassDocumentValidation'], 'boolean');
        }

        if ( ! is_bool($options['ordered'])) {
            throw InvalidArgumentException::invalidType('"ordered" option', $options['ordered'], 'boolean');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->operations = $operations;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return BulkWriteResult
     */
    public function execute(Server $server)
    {
        $options = ['ordered' => $this->options['ordered']];

        if (isset($this->options['bypassDocumentValidation']) && \MongoDB\server_supports_feature($server, self::$wireVersionForDocumentLevelValidation)) {
            $options['bypassDocumentValidation'] = $this->options['bypassDocumentValidation'];
        }

        $bulk = new Bulk($options);
        $insertedIds = [];

        foreach ($this->operations as $i => $operation) {
            if ($operation instanceof InsertOneInput) {
                $insertedId = $operation->addToBulk($bulk);
                if ($insertedId !== null) {
                    $insertedIds[$i] = $insertedId;
                } else {
                    $insertedIds[$i] = \MongoDB\extract_id_from_inserted_document($operation->getDocument());
                }
                
                continue;
            }
            
            $operation->addToBulk($bulk);
        }

        $writeConcern = isset($this->options['writeConcern']) ? $this->options['writeConcern'] : null;
        $writeResult = $server->executeBulkWrite($this->databaseName . '.' . $this->collectionName, $bulk, $writeConcern);

        return new BulkWriteResult($writeResult, $insertedIds);
    }
}
