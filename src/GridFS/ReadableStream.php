<?php
/*
 * Copyright 2016-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\GridFS;

use MongoDB\Exception\InvalidArgumentException;
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
    private $chunkSize;
    private $chunkOffset = 0;
    private $chunksIterator;
    private $collectionWrapper;
    private $file;
    private $firstCheck = true;
    private $iteratorEmpty = false;
    private $length;
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
        if ( ! isset($file->chunkSize) || ! is_integer($file->chunkSize) || $file->chunkSize < 1) {
            throw new CorruptFileException('file.chunkSize is not an integer >= 1');
        }

        if ( ! isset($file->length) || ! is_integer($file->length) || $file->length < 0) {
            throw new CorruptFileException('file.length is not an integer > 0');
        }

        if ( ! isset($file->_id) && ! property_exists($file, '_id')) {
            throw new CorruptFileException('file._id does not exist');
        }

        $this->file = $file;
        $this->chunkSize = $file->chunkSize;
        $this->length = $file->length;

        $this->chunksIterator = $collectionWrapper->getChunksIteratorByFilesId($file->_id);
        $this->collectionWrapper = $collectionWrapper;
        $this->numChunks = ceil($this->length / $this->chunkSize);
        $this->initEmptyBuffer();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'bucketName' => $this->collectionWrapper->getBucketName(),
            'databaseName' => $this->collectionWrapper->getDatabaseName(),
            'file' => $this->file,
        ];
    }

    public function close()
    {
        if (is_resource($this->buffer)) {
            @fclose($this->buffer);
        }
    }

    /**
     * Read bytes from the stream.
     *
     * Note: this method may return a string smaller than the requested length
     * if data is not available to be read.
     * 
     * @param integer $numBytes Number of bytes to read
     * @return string
     * @throws InvalidArgumentException if $numBytes is negative
     */
    public function downloadNumBytes($numBytes)
    {
        if ($numBytes < 0) {
            throw new InvalidArgumentException(sprintf('$numBytes must be >= zero; given: %d', $numBytes));
        }

        if ($numBytes == 0) {
            return '';
        }

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

        if ( ! $this->iteratorEmpty && $this->length > 0 && $bytesLeft < strlen($this->chunksIterator->current()->data->getData())) {
            fwrite($this->buffer, substr($this->chunksIterator->current()->data->getData(), $bytesLeft));
            $this->bufferEmpty = false;
        }

        return $output;
    }

    /**
     * Return the stream's file document.
     *
     * @return stdClass
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Return the stream's size in bytes.
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->length;
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
            ? ($this->length - $this->bytesSeen)
            : $this->chunkSize;

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

        $this->buffer = fopen("php://memory", "w+b");
        $this->bufferEmpty = true;
        $this->bufferFresh = true;
    }
}
