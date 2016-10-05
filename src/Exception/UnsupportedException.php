<?php

namespace MongoDB\Exception;

class UnsupportedException extends RuntimeException implements Exception
{
    /**
     * Thrown when collations are not supported by a server.
     *
     * @return self
     */
    public static function collationNotSupported()
    {
        return new static('Collations are not supported by the server executing this operation');
    }

    /**
     * Thrown when a command's writeConcern option is not supported by a server.
     *
     * @return self
     */
    public static function writeConcernNotSupported()
    {
        return new static('Write concern is not supported by the server executing this command');
    }
}
