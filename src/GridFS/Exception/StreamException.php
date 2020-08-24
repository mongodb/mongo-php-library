<?php

namespace MongoDB\GridFS\Exception;

use MongoDB\Exception\RuntimeException;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toJSON;
use function sprintf;
use function stream_get_meta_data;

class StreamException extends RuntimeException
{
    /** @param resource $destination */
    public static function downloadFromFilenameFailed(string $filename, $destination) : self
    {
        $metadata = stream_get_meta_data($destination);

        return new static(sprintf('Downloading GridFS file "%s" to resource "%s" failed: stream error.', $filename, $metadata['uri']));
    }

    /**
     * @param mixed    $id
     * @param resource $destination
     */
    public static function downloadFromIdFailed($id, $destination) : self
    {
        $stringId = toJSON(fromPHP(['_id' => $id]));
        $metadata = stream_get_meta_data($destination);

        return new static(sprintf('Downloading GridFS file with identifier "%s" to resource "%s" failed: stream error.', $stringId, $metadata['uri']));
    }

    /** @param resource $source */
    public static function uploadFailed(string $filename, $source) : self
    {
        $metadata = stream_get_meta_data($source);

        return new static(sprintf('Uploading file from resource "%s" to GridFS file "%s" failed: stream error.', $metadata['uri'], $filename));
    }
}
