<?php

namespace MongoDB;

use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentTypeException;
use ReflectionClass;

/**
 * Return whether the first key in the document starts with a "$" character.
 *
 * This is used for differentiating update and replacement documents.
 *
 * @internal
 * @param array|object $document Update or replacement document
 * @return boolean
 * @throws InvalidArgumentTypeException
 */
function is_first_key_operator($document)
{
    if (is_object($document)) {
        $document = get_object_vars($document);
    }

    if ( ! is_array($document)) {
        throw new InvalidArgumentTypeException('$document', $document, 'array or object');
    }

    $firstKey = (string) key($document);

    return (isset($firstKey[0]) && $firstKey[0] == '$');
}

/**
 * Return whether the aggregation pipeline ends with an $out operator.
 *
 * This is used for determining whether the aggregation pipeline msut be
 * executed against a primary server.
 *
 * @internal
 * @param array $pipeline List of pipeline operations
 * @return boolean
 */
function is_last_pipeline_operator_out(array $pipeline)
{
    $lastOp = end($pipeline);

    if ($lastOp === false) {
        return false;
    }

    $lastOp = (array) $lastOp;

    return key($lastOp) === '$out';
}

/**
 * Returns a ReadPreference corresponding to the Manager's read preference.
 *
 * @internal
 * @todo this function can be removed once PHPC-417 is implemented
 * @param Manager $manager
 * @return ReadPreference
 */
function get_manager_read_preference(Manager $manager)
{
    $rp = $manager->getReadPreference();

    if ($rp instanceof ReadPreference) {
        return $rp;
    }

    $args = array(
        $rp['mode'],
    );

    if (isset($rp['tags'])) {
        $args[] = $rp['tags'];
    }

    $rc = new ReflectionClass('MongoDB\Driver\ReadPreference');

    return $rc->newInstanceArgs($args);
}

/**
 * Returns a WriteConcern corresponding to the Manager's write concern.
 *
 * @internal
 * @todo this function can be removed once PHPC-417 is implemented
 * @param Manager $manager
 * @return WriteConcern
 */
function get_manager_write_concern(Manager $manager)
{
    $wc = $manager->getWriteConcern();

    if ($wc instanceof WriteConcern) {
        return $wc;
    }

    $args = array(
        isset($wc['w']) ? $wc['w'] : -2,
        $wc['wtimeout'],
    );

    if (isset($wc['journal'])) {
        $args[] = $wc['journal'];

        if (isset($wc['fsync'])) {
            $args[] = $wc['fsync'];
        }
    }

    $rc = new ReflectionClass('MongoDB\Driver\WriteConcern');

    return $rc->newInstanceArgs($args);
}

/**
 * Generate an index name from a key specification.
 *
 * @internal
 * @param array|object $document Document containing fields mapped to values,
 *                               which denote order or an index type
 * @return string
 * @throws InvalidArgumentTypeException
 */
function generate_index_name($document)
{
    if (is_object($document)) {
        $document = get_object_vars($document);
    }

    if ( ! is_array($document)) {
        throw new InvalidArgumentTypeException('$document', $document, 'array or object');
    }

    $name = '';

    foreach ($document as $field => $type) {
        $name .= ($name != '' ? '_' : '') . $field . '_' . $type;
    }

    return $name;
}

/**
 * Return whether the server supports a particular feature.
 *
 * @internal
 * @param Server  $server  Server to check
 * @param integer $feature Feature constant (i.e. wire protocol version)
 * @return boolean
 */
function server_supports_feature(Server $server, $feature)
{
    $info = $server->getInfo();
    $maxWireVersion = isset($info['maxWireVersion']) ? (integer) $info['maxWireVersion'] : 0;
    $minWireVersion = isset($info['minWireVersion']) ? (integer) $info['minWireVersion'] : 0;

    return ($minWireVersion <= $feature && $maxWireVersion >= $feature);
}
