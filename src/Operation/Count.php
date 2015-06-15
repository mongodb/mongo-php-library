<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedValueException;

/**
 * Operation for the count command.
 *
 * @api
 * @see MongoDB\Collection::count()
 * @see http://docs.mongodb.org/manual/reference/command/count/
 */
class Count implements Executable
{
    private $databaseName;
    private $collectionName;
    private $filter;
    private $options;

    /**
     * Constructs a count command.
     *
     * Supported options:
     *
     *  * hint (string|document): The index to use. If a document, it will be
     *    interpretted as an index specification and a name will be generated.
     *
     *  * limit (integer): The maximum number of documents to count.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * skip (integer): The number of documents to skip before returning the
     *    documents.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $filter         Query by which to filter documents
     * @param array  $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, array $filter = array(), array $options = array())
    {
        if (isset($options['hint'])) {
            if (is_array($options['hint']) || is_object($options['hint'])) {
                $options['hint'] = \MongoDB\generate_index_name($options['hint']);
            }

            if ( ! is_string($options['hint'])) {
                throw new InvalidArgumentTypeException('"hint" option', $options['hint'], 'string or array or object');
            }
        }

        if (isset($options['limit']) && ! is_integer($options['limit'])) {
            throw new InvalidArgumentTypeException('"limit" option', $options['limit'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw new InvalidArgumentTypeException('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['skip']) && ! is_integer($options['skip'])) {
            throw new InvalidArgumentTypeException('"skip" option', $options['skip'], 'integer');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->filter = $filter;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return integer
     */
    public function execute(Server $server)
    {
        $cursor = $server->executeCommand($this->databaseName, $this->createCommand());
        $result = current($cursor->toArray());

        if (empty($result['ok'])) {
            throw new RuntimeException(isset($result['errmsg']) ? $result['errmsg'] : 'Unknown error');
        }

        if ( ! isset($result['n']) || ! is_integer($result['n'])) {
            throw new UnexpectedValueException('count command did not return an "n" integer');
        }

        return $result['n'];
    }

    /**
     * Create the count command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = array(
            'count' => $this->collectionName,
        );

        if ( ! empty($this->filter)) {
            $cmd['query'] = (object) $this->filter;
        }

        foreach (array('hint', 'limit', 'maxTimeMS', 'skip') as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        return new Command($cmd);
    }
}
