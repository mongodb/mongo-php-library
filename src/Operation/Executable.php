<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Server;

/**
 * Executable interface for operation classes.
 *
 * @api
 */
interface Executable
{
    /**
     * Execute the operation.
     *
     * @param Server $server
     * @return mixed
     */
    public function execute(Server $server);
}
