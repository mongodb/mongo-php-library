<?php

namespace MongoDB;

use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentTypeException;

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
