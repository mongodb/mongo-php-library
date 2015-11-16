<?php

namespace MongoDB\Exception;

class BadMethodCallException extends \BadMethodCallException implements Exception
{
    /**
     * Thrown when accessing a result field on an unacknowledged write result.
     */
    public static function unacknowledgedWriteResultAccess($method)
    {
        return new static(sprintf('%s should not be called for an unacknowledged write result', $method));
    }
}
