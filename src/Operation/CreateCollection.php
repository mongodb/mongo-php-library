<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedTypeException;
use MongoDB\Model\IndexInput;

/**
 * Operation for the create command.
 *
 * @api
 * @see MongoDB\Database::createCollection()
 * @see http://docs.mongodb.org/manual/reference/command/create/
 */
class CreateCollection implements Executable
{
    const USE_POWER_OF_2_SIZES = 1;
    const NO_PADDING = 2;

    private $databaseName;
    private $collectionName;
    private $options = array();

    /**
     * Constructs a create command.
     *
     * Supported options:
     *
     *  * autoIndexId (boolean): Specify false to disable the automatic creation
     *    of an index on the _id field. For replica sets, this option cannot be
     *    false. The default is true.
     *
     *  * capped (boolean): Specify true to create a capped collection. If set,
     *    the size option must also be specified. The default is false.
     *
     *  * flags (integer): Options for the MMAPv1 storage engine only. Must be a
     *    bitwise combination USE_POWER_OF_2_SIZES and NO_PADDING. The default
     *    is USE_POWER_OF_2_SIZES.
     *
     *  * max (integer): The maximum number of documents allowed in the capped
     *    collection. The size option takes precedence over this limit.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * size (integer): The maximum number of bytes for a capped collection.
     *
     *  * storageEngine (document): Storage engine options.
     *
     * @see http://source.wiredtiger.com/2.4.1/struct_w_t___s_e_s_s_i_o_n.html#a358ca4141d59c345f401c58501276bbb
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, array $options = array())
    {
        if (isset($options['autoIndexId']) && ! is_bool($options['autoIndexId'])) {
            throw new InvalidArgumentTypeException('"autoIndexId" option', $options['autoIndexId'], 'boolean');
        }

        if (isset($options['capped']) && ! is_bool($options['capped'])) {
            throw new InvalidArgumentTypeException('"capped" option', $options['capped'], 'boolean');
        }

        if (isset($options['flags']) && ! is_integer($options['flags'])) {
            throw new InvalidArgumentTypeException('"flags" option', $options['flags'], 'integer');
        }

        if (isset($options['max']) && ! is_integer($options['max'])) {
            throw new InvalidArgumentTypeException('"max" option', $options['max'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw new InvalidArgumentTypeException('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['size']) && ! is_integer($options['size'])) {
            throw new InvalidArgumentTypeException('"size" option', $options['size'], 'integer');
        }

        if (isset($options['storageEngine']) && ! is_array($options['storageEngine']) && ! is_object($options['storageEngine'])) {
            throw new InvalidArgumentTypeException('"storageEngine" option', $options['storageEngine'], 'array or object');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * For servers < 2.6, this will actually perform an insert operation on the
     * database's "system.indexes" collection.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return object Command result document
     */
    public function execute(Server $server)
    {
        $cursor = $server->executeCommand($this->databaseName, $this->createCommand());
        $result = current($cursor->toArray());

        if (empty($result['ok'])) {
            throw new RuntimeException(isset($result['errmsg']) ? $result['errmsg'] : 'Unknown error');
        }

        return $result;
    }

    /**
     * Create the create command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = array('create' => $this->collectionName);

        foreach (array('autoIndexId', 'capped', 'flags', 'max', 'maxTimeMS', 'size') as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        if ( ! empty($this->options['storageEngine'])) {
            $cmd['storageEngine'] = (object) $this->options['storageEngine'];
        }

        return new Command($cmd);
    }
}
