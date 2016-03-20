<?php

namespace MongoDB\GridFS;

use MongoDB\BSON\Binary;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Exception\InvalidArgumentException;

/**
 * GridFSUpload abstracts the process of writing a GridFS file.
 *
 * @internal
 */
class GridFSUpload
{
    private $buffer;
    private $bufferLength = 0;
    private $chunkOffset = 0;
    private $chunkSize;
    private $collectionsWrapper;
    private $ctx;
    private $file;
    private $indexChecker;
    private $isClosed = false;
    private $length = 0;

    /**
     * Constructs a GridFS upload stream.
     *
     * Supported options:
     *
     *  * aliases (array of strings): DEPRECATED An array of aliases. 
     *    Applications wishing to store aliases should add an aliases field to
     *    the metadata document instead.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to
     *    261120 (i.e. 255 KiB).
     *
     *  * contentType (string): DEPRECATED content type to be stored with the
     *    file. This information should now be added to the metadata.
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param GridFSCollectionsWrapper $collectionsWrapper GridFS collections wrapper
     * @param string                   $filename           File name
     * @param array                    $options            Upload options
     * @throws InvalidArgumentException
     */
    public function __construct(GridFSCollectionsWrapper $collectionsWrapper, $filename, array $options = [])
    {
        $options += ['chunkSizeBytes' => 261120];

        if (isset($options['aliases']) && ! \MongoDB\is_string_array($options['aliases'])) {
            throw InvalidArgumentException::invalidType('"aliases" option', $options['aliases'], 'array of strings');
        }

        if (isset($options['contentType']) && ! is_string($options['contentType'])) {
            throw InvalidArgumentException::invalidType('"contentType" option', $options['contentType'], 'string');
        }

        if (isset($options['metadata']) && ! is_array($options['metadata']) && ! is_object($options['metadata'])) {
            throw InvalidArgumentException::invalidType('"metadata" option', $options['metadata'], 'array or object');
        }

        $this->chunkSize = $options['chunkSizeBytes'];
        $this->collectionsWrapper = $collectionsWrapper;
        $this->buffer = fopen('php://temp', 'w+');
        $this->ctx = hash_init('md5');

        $this->file = [
            '_id' => new ObjectId(),
            'chunkSize' => $this->chunkSize,
            'filename' => (string) $filename,
            'uploadDate' => $this->createUploadDate(),
        ] + array_intersect_key($options, ['aliases' => 1, 'contentType' => 1, 'metadata' => 1]);
    }

    /**
     * Closes an active stream and flushes all buffered data to GridFS.
     */
    public function close()
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return;
        }

        rewind($this->buffer);
        $cached = stream_get_contents($this->buffer);

        if (strlen($cached) > 0) {
            $this->insertChunk($cached);
        }

        fclose($this->buffer);
        $this->fileCollectionInsert();
        $this->isClosed = true;
    }

    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getId()
    {
        return $this->file['_id'];
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getSize()
    {
        return $this->length;
    }

    /**
     * Inserts binary data into GridFS via chunks.
     *
     * Data will be buffered internally until chunkSizeBytes are accumulated, at
     * which point a chunk's worth of data will be inserted and the buffer
     * reset.
     *
     * @param string $toWrite Binary data to write
     * @return int
     */
    public function insertChunks($toWrite)
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return;
        }

        $readBytes = 0;

        while ($readBytes != strlen($toWrite)) {
            $addToBuffer = substr($toWrite, $readBytes, $this->chunkSize - $this->bufferLength);
            fwrite($this->buffer, $addToBuffer);
            $readBytes += strlen($addToBuffer);
            $this->bufferLength += strlen($addToBuffer);

            if ($this->bufferLength == $this->chunkSize) {
                rewind($this->buffer);
                $this->insertChunk(stream_get_contents($this->buffer));
                ftruncate($this->buffer, 0);
                $this->bufferLength = 0;
            }
        }

        return $readBytes;
    }

    public function isEOF()
    {
        return $this->isClosed;
    }

    /**
     * Writes the contents of a readable stream to a GridFS file.
     *
     * @param resource $source Readable stream
     * @return ObjectId
     */
    public function uploadFromStream($source)
    {
        if ( ! is_resource($source) || get_resource_type($source) != "stream") {
            throw InvalidArgumentException::invalidType('$source', $source, 'resource');
        }

        $streamMetadata = stream_get_meta_data($source);

        while ($data = $this->readChunk($source)) {
            $this->insertChunk($data);
        }

        return $this->fileCollectionInsert();
    }

    private function abort()
    {
        $this->collectionsWrapper->getChunksCollection()->deleteMany(['files_id' => $this->file['_id']]);
        $this->collectionsWrapper->getFilesCollection()->deleteOne(['_id' => $this->file['_id']]);
        $this->isClosed = true;
    }

    // From: http://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
    private function createUploadDate()
    {
        $parts = explode(' ', microtime());
        $milliseconds = sprintf('%d%03d', $parts[1], $parts[0] * 1000);

        return new UTCDateTime($milliseconds);
    }

    private function fileCollectionInsert()
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return;
        }

        $md5 = hash_final($this->ctx);

        $this->file['length'] = $this->length;
        $this->file['md5'] = $md5;

        $this->collectionsWrapper->insertFile($this->file);

        return $this->file['_id'];
    }

    private function insertChunk($data)
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return;
        }

        $toUpload = [
            'files_id' => $this->file['_id'],
            'n' => $this->chunkOffset,
            'data' => new Binary($data, Binary::TYPE_GENERIC),
        ];

        hash_update($this->ctx, $data);

        $this->collectionsWrapper->insertChunk($toUpload);
        $this->length += strlen($data);
        $this->chunkOffset++;
    }

    private function readChunk($source)
    {
        try {
            $data = fread($source, $this->chunkSize);
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        return $data;
    }
}
