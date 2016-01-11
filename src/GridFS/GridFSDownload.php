<?php

namespace MongoDB\GridFS;

use MongoDB\Driver\Exception\Exception;
use MongoDB\Exception\GridFSCorruptFileException;
use stdClass;

/**
 * GridFSDownload abstracts the process of reading a GridFS file.
 *
 * @internal
 */
class GridFSDownload
{
    private $buffer;
    private $bufferEmpty = true;
    private $bufferFresh = true;
    private $bytesSeen = 0;
    private $chunkOffset = 0;
    private $chunksIterator;
    private $collectionsWrapper;
    private $file;
    private $firstCheck = true;
    private $iteratorEmpty = false;
    private $numChunks;

    /**
     * Constructs a GridFS download stream.
     *
     * @param GridFSCollectionsWrapper $collectionsWrapper GridFS collections wrapper
     * @param stdClass                 $file               GridFS file document
     * @throws GridFSCorruptFileException
     */
    public function __construct(GridFSCollectionsWrapper $collectionsWrapper, stdClass $file)
    {
        $this->collectionsWrapper = $collectionsWrapper;
        $this->file = $file;

        try {
            $cursor = $this->collectionsWrapper->getChunksCollection()->find(
                ['files_id' => $this->file->_id],
                ['sort' => ['n' => 1]]
            );
        } catch (Exception $e) {
            // TODO: Why do we replace a driver exception with GridFSCorruptFileException here?
            throw new GridFSCorruptFileException();
        }

        $this->chunksIterator = new \IteratorIterator($cursor);
        $this->numChunks = ($file->length >= 0) ? ceil($file->length / $file->chunkSize) : 0;
        $this->buffer = fopen('php://temp', 'w+');
    }

    public function close()
    {
        fclose($this->buffer);
    }

    public function downloadNumBytes($numToRead)
    {
        $output = "";

        if ($this->bufferFresh) {
            rewind($this->buffer);
            $this->bufferFresh = false;
        }

        // TODO: Should we be checking for fread errors here?
        $output = fread($this->buffer, $numToRead);

        if (strlen($output) == $numToRead) {
            return $output;
        }

        fclose($this->buffer);
        $this->buffer = fopen("php://temp", "w+");

        $this->bufferFresh = true;
        $this->bufferEmpty = true;

        $bytesLeft = $numToRead - strlen($output);

        while (strlen($output) < $numToRead && $this->advanceChunks()) {
            $bytesLeft = $numToRead - strlen($output);
            $output .= substr($this->chunksIterator->current()->data->getData(), 0, $bytesLeft);
        }

        if ( ! $this->iteratorEmpty && $this->file->length > 0 && $bytesLeft < strlen($this->chunksIterator->current()->data->getData())) {
            fwrite($this->buffer, substr($this->chunksIterator->current()->data->getData(), $bytesLeft));
            $this->bufferEmpty = false;
        }

        return $output;
    }

    public function downloadToStream($destination)
    {
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
            throw new GridFSCorruptFileException();
        }

        if ($this->chunksIterator->current()->n != $this->chunkOffset) {
            throw new GridFSCorruptFileException();
        }

        $actualChunkSize = strlen($this->chunksIterator->current()->data->getData());

        $expectedChunkSize = ($this->chunkOffset == $this->numChunks - 1)
            ? ($this->file->length - $this->bytesSeen)
            : $this->file->chunkSize;

        if ($actualChunkSize != $expectedChunkSize) {
            throw new GridFSCorruptFileException();
        }

        $this->bytesSeen += $actualChunkSize;
        $this->chunkOffset++;

        return true;
    }
}
