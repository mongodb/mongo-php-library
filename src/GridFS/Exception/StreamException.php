<?php

namespace MongoDB\GridFS\Exception;

use MongoDB\Exception\RuntimeException;

class StreamException extends RuntimeException
{
    public static function downloadFailed() : self
    {
        return new static('Downloading file from GridFS failed: stream error.');
    }

    public static function uploadFailed() : self
    {
        return new static('Uploading file to GridFS failed: stream error.');
    }
}
