<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedValueException;

/**
 * Operation for the distinct command.
 *
 * @api
 * @see MongoDB\Collection::distinct()
 * @see http://docs.mongodb.org/manual/reference/command/distinct/
 */
class Distinct implements Executable
{
    private $databaseName;
    private $collectionName;
    private $fieldName;
    private $filter;
    private $options;

    /**
     * Constructs a distinct command.
     *
     * Supported options:
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param string $fieldName      Field for which to return distinct values
     * @param array  $filter         Query by which to filter documents
     * @param array  $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, $fieldName, array $filter = array(), array $options = array())
    {
        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw new InvalidArgumentTypeException('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->fieldName = (string) $fieldName;
        $this->filter = $filter;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return mixed[]
     */
    public function execute(Server $server)
    {
        $cursor = $server->executeCommand($this->databaseName, $this->createCommand());
        $result = current($cursor->toArray());

        if (empty($result['ok'])) {
            throw new RuntimeException(isset($result['errmsg']) ? $result['errmsg'] : 'Unknown error');
        }

        if ( ! isset($result['values']) || ! is_array($result['values'])) {
            throw new UnexpectedValueException('distinct command did not return a "values" array');
        }

        return $result['values'];
    }

    /**
     * Create the distinct command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = array(
            'distinct' => $this->collectionName,
            'key' => $this->fieldName,
        );

        if ( ! empty($this->filter)) {
            $cmd['query'] = (object) $this->filter;
        }

        if (isset($this->options['maxTimeMS'])) {
            $cmd[$option] = $this->options['maxTimeMS'];
        }

        return new Command($cmd);
    }
}
