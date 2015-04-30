<?php

namespace MongoDB;

use MongoDB\Driver\Server;

/**
 * Utility class for detecting features based on wire protocol versions.
 *
 * @internal
 */
class FeatureDetection
{
    const API_LISTCOLLECTIONS_CMD = 3;
    const API_LISTINDEXES_CMD = 3;
    const API_CREATEINDEXES_CMD = 2;
    const API_AGGREGATE_CURSOR = 2;

    /**
     * Return whether the server supports a particular feature.
     *
     * @param Server  $server  Server to check
     * @param integer $feature Feature constant (i.e. wire protocol version)
     * @return boolean
     */
    static public function isSupported(Server $server, $feature)
    {
        $info = $server->getInfo();
        $maxWireVersion = isset($info['maxWireVersion']) ? (integer) $info['maxWireVersion'] : 0;
        $minWireVersion = isset($info['minWireVersion']) ? (integer) $info['minWireVersion'] : 0;

        return ($minWireVersion <= $feature && $maxWireVersion >= $feature);
    }
}
