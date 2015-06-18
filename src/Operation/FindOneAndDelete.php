<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;

/**
 * Operation for deleting a document with the findAndModify command.
 *
 * @api
 * @see MongoDB\Collection::findOneAndDelete()
 * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
 */
class FindOneAndDelete implements Executable
{
    private $findAndModify;

    /**
     * Constructs a findAndModify command for deleting a document.
     *
     * Supported options:
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * projection (document): Limits the fields to return for the matching
     *    document.
     *
     *  * sort (document): Determines which document the operation modifies if
     *    the query selects multiple documents.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, $filter, array $options = array())
    {
        if ( ! is_array($filter) && ! is_object($filter)) {
            throw new InvalidArgumentTypeException('$filter', $filter, 'array or object');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw new InvalidArgumentTypeException('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['projection']) && ! is_array($options['projection']) && ! is_object($options['projection'])) {
            throw new InvalidArgumentTypeException('"projection" option', $options['projection'], 'array or object');
        }

        if (isset($options['sort']) && ! is_array($options['sort']) && ! is_object($options['sort'])) {
            throw new InvalidArgumentTypeException('"sort" option', $options['sort'], 'array or object');
        }

        $this->findAndModify = new FindAndModify(
            $databaseName,
            $collectionName,
            array(
                'fields' => isset($options['projection']) ? $options['projection'] : null,
                'maxTimeMS' => isset($options['maxTimeMS']) ? $options['maxTimeMS'] : null,
                'query' => $filter,
                'remove' => true,
                'sort' => isset($options['sort']) ? $options['sort'] : null,
            )
        );
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return object|null
     */
    public function execute(Server $server)
    {
        return $this->findAndModify->execute($server);
    }
}
