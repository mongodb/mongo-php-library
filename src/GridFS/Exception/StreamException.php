<?php

namespace MongoDB\GridFS\Exception;

use MongoDB\BSON\Document;
use MongoDB\Exception\RuntimeException;

use function sprintf;
use function stream_get_meta_data;

class StreamException extends RuntimeException
{
    /**
     * @param resource $source
     * @param resource $destination
     */
    public static function downloadFromFilenameFailed(string $filename, $source, $destination): self
    {
        $sourceMetadata = stream_get_meta_data($source);
        $destinationMetadata = stream_get_meta_data($destination);

        return new self(sprintf('Downloading file from "%s" to "%s" failed. GridFS filename: "%s"', $sourceMetadata['uri'], $destinationMetadata['uri'], $filename));
    }

    /**
     * @param resource $source
     * @param resource $destination
     */
    public static function downloadFromIdFailed(mixed $id, $source, $destination): self
    {
        $idString = Document::fromPHP(['_id' => $id])->toRelaxedExtendedJSON();
        $sourceMetadata = stream_get_meta_data($source);
        $destinationMetadata = stream_get_meta_data($destination);

        return new self(sprintf('Downloading file from "%s" to "%s" failed. GridFS identifier: "%s"', $sourceMetadata['uri'], $destinationMetadata['uri'], $idString));
    }

    /** @param resource $source */
    public static function uploadFailed(string $filename, $source, string $destinationUri): self
    {
        $sourceMetadata = stream_get_meta_data($source);

        return new self(sprintf('Uploading file from "%s" to "%s" failed. GridFS filename: "%s"', $sourceMetadata['uri'], $destinationUri, $filename));
    }
}
