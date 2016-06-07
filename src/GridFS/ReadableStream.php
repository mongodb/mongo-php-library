<?php

namespace MongoDB\GridFS;

use MongoDB\Driver\Exception\Exception;
use MongoDB\GridFS\Exception\CorruptFileException;
use stdClass;

/**
 * ReadableStream abstracts the process of reading a GridFS file.
 *
 * @internal
 */
class ReadableStream
{
    private $buffer;
    private $bufferEmpty;
    private $bufferFresh;
    private $bytesSeen = 0;
    private $chunkOffset = 0;
    private $chunksIterator;
    private $file;
    private $firstCheck = true;
    private $iteratorEmpty = false;
    private $numChunks;

    /**
     * Constructs a readable GridFS stream.
     *
     * @param CollectionWrapper $collectionWrapper GridFS collection wrapper
     * @param stdClass          $file              GridFS file document
     * @throws CorruptFileException
     */
    public function __construct(CollectionWrapper $collectionWrapper, stdClass $file)
    {
        $this->file = $file;

        $this->chunksIterator = $collectionWrapper->getChunksIteratorByFilesId($this->file->_id);
        $this->numChunks = ($file->length >= 0) ? ceil($file->length / $file->chunkSize) : 0;
        $this->initEmptyBuffer();
    }

    public function close()
    {
        fclose($this->buffer);
    }

    /**
     * Read bytes from the stream.
     *
     * Note: this method may return a string smaller than the requested length
     * if data is not available to be read.
     * 
     * @param integer $numBytes Number of bytes to read
     * @return string 
     */
    public function downloadNumBytes($numBytes)
    {
        if ($this->bufferFresh) {
            rewind($this->buffer);
            $this->bufferFresh = false;
        }

        // TODO: Should we be checking for fread errors here?
        $output = fread($this->buffer, $numBytes);

        if (strlen($output) == $numBytes) {
            return $output;
        }

        $this->initEmptyBuffer();

        $bytesLeft = $numBytes - strlen($output);

        while (strlen($output) < $numBytes && $this->advanceChunks()) {
            $bytesLeft = $numBytes - strlen($output);
            $output .= substr($this->chunksIterator->current()->data->getData(), 0, $bytesLeft);
        }

        if ( ! $this->iteratorEmpty && $this->file->length > 0 && $bytesLeft < strlen($this->chunksIterator->current()->data->getData())) {
            fwrite($this->buffer, substr($this->chunksIterator->current()->data->getData(), $bytesLeft));
            $this->bufferEmpty = false;
        }

        return $output;
    }

    /**
     * Writes the contents of this GridFS file to a writable stream.
     *
     * @param resource $destination Writable stream
     * @throws InvalidArgumentException
     */
    public function downloadToStream($destination)
    {
        if ( ! is_resource($destination) || get_resource_type($destination) != "stream") {
            throw InvalidArgumentException::invalidType('$destination', $destination, 'resource');
        }

        while ($this->advanceChunks()) {
            // TODO: Should we be checking for fwrite errors here?
            fwrite($destination, $this->chunksIterator->current()->data->getData());
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getId()
    {
        return $this->file->_id;
    }

    public function getSize()
    {
        return $this->file->length;
    }

    public function isEOF()
    {
        return ($this->iteratorEmpty && $this->bufferEmpty);
    }

    private function advanceChunks()
    {
        if ($this->chunkOffset >= $this->numChunks) {
            $this->iteratorEmpty = true;

            return false;
        }

        if ($this->firstCheck) {
            $this->chunksIterator->rewind();
            $this->firstCheck = false;
        } else {
            $this->chunksIterator->next();
        }

        if ( ! $this->chunksIterator->valid()) {
            throw CorruptFileException::missingChunk($this->chunkOffset);
        }

        if ($this->chunksIterator->current()->n != $this->chunkOffset) {
            throw CorruptFileException::unexpectedIndex($this->chunksIterator->current()->n, $this->chunkOffset);
        }

        $actualChunkSize = strlen($this->chunksIterator->current()->data->getData());

        $expectedChunkSize = ($this->chunkOffset == $this->numChunks - 1)
            ? ($this->file->length - $this->bytesSeen)
            : $this->file->chunkSize;

        if ($actualChunkSize != $expectedChunkSize) {
            throw CorruptFileException::unexpectedSize($actualChunkSize, $expectedChunkSize);
        }

        $this->bytesSeen += $actualChunkSize;
        $this->chunkOffset++;

        return true;
    }

    private function initEmptyBuffer()
    {
        if (isset($this->buffer)) {
            fclose($this->buffer);
        }

        $this->buffer = fopen("php://temp", "w+");
        $this->bufferEmpty = true;
        $this->bufferFresh = true;
    }
}
