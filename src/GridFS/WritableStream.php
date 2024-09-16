<?php
/*
 * Copyright 2016-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\GridFS;

use MongoDB\BSON\Binary;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;

use function array_intersect_key;
use function is_integer;
use function MongoDB\is_document;
use function sprintf;
use function strlen;
use function substr;

/**
 * WritableStream abstracts the process of writing a GridFS file.
 *
 * @internal
 */
final class WritableStream
{
    private const DEFAULT_CHUNK_SIZE_BYTES = 261120;

    private string $buffer = '';

    private int $chunkOffset = 0;

    private int $chunkSize;

    private array $file;

    private bool $isClosed = false;

    private int $length = 0;

    /**
     * Constructs a writable GridFS stream.
     *
     * Supported options:
     *
     *  * _id (mixed): File document identifier. Defaults to a new ObjectId.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to
     *    261120 (i.e. 255 KiB).
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param CollectionWrapper $collectionWrapper GridFS collection wrapper
     * @param string            $filename          Filename
     * @param array             $options           Upload options
     * @throws InvalidArgumentException
     */
    public function __construct(private CollectionWrapper $collectionWrapper, string $filename, array $options = [])
    {
        $options += [
            '_id' => new ObjectId(),
            'chunkSizeBytes' => self::DEFAULT_CHUNK_SIZE_BYTES,
        ];

        if (! is_integer($options['chunkSizeBytes'])) {
            throw InvalidArgumentException::invalidType('"chunkSizeBytes" option', $options['chunkSizeBytes'], 'integer');
        }

        if ($options['chunkSizeBytes'] < 1) {
            throw new InvalidArgumentException(sprintf('Expected "chunkSizeBytes" option to be >= 1, %d given', $options['chunkSizeBytes']));
        }

        if (isset($options['metadata']) && ! is_document($options['metadata'])) {
            throw InvalidArgumentException::expectedDocumentType('"metadata" option', $options['metadata']);
        }

        $this->chunkSize = $options['chunkSizeBytes'];
        $this->file = [
            '_id' => $options['_id'],
            'chunkSize' => $this->chunkSize,
            'filename' => $filename,
            'length' => null,
            'uploadDate' => null,
        ] + array_intersect_key($options, ['metadata' => 1]);
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return [
            'bucketName' => $this->collectionWrapper->getBucketName(),
            'databaseName' => $this->collectionWrapper->getDatabaseName(),
            'file' => $this->file,
        ];
    }

    /**
     * Closes an active stream and flushes all buffered data to GridFS.
     */
    public function close(): void
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return;
        }

        if (strlen($this->buffer) > 0) {
            $this->insertChunkFromBuffer();
        }

        $this->fileCollectionInsert();
        $this->isClosed = true;
    }

    /**
     * Return the stream's file document.
     */
    public function getFile(): object
    {
        return (object) $this->file;
    }

    /**
     * Return the stream's size in bytes.
     *
     * Note: this value will increase as more data is written to the stream.
     */
    public function getSize(): int
    {
        return $this->length + strlen($this->buffer);
    }

    /**
     * Return the current position of the stream.
     *
     * This is the offset within the stream where the next byte would be
     * written. Since seeking is not supported and writes are appended, this is
     * always the end of the stream.
     *
     * @see WritableStream::getSize()
     */
    public function tell(): int
    {
        return $this->getSize();
    }

    /**
     * Inserts binary data into GridFS via chunks.
     *
     * Data will be buffered internally until chunkSizeBytes are accumulated, at
     * which point a chunk document will be inserted and the buffer reset.
     *
     * @param string $data Binary data to write
     */
    public function writeBytes(string $data): int
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return 0;
        }

        $bytesRead = 0;

        while ($bytesRead != strlen($data)) {
            $initialBufferLength = strlen($this->buffer);
            $this->buffer .= substr($data, $bytesRead, $this->chunkSize - $initialBufferLength);
            $bytesRead += strlen($this->buffer) - $initialBufferLength;

            if (strlen($this->buffer) == $this->chunkSize) {
                $this->insertChunkFromBuffer();
            }
        }

        return $bytesRead;
    }

    private function abort(): void
    {
        try {
            $this->collectionWrapper->deleteChunksByFilesId($this->file['_id']);
        } catch (DriverRuntimeException) {
            // We are already handling an error if abort() is called, so suppress this
        }

        $this->isClosed = true;
    }

    private function fileCollectionInsert(): void
    {
        $this->file['length'] = $this->length;
        $this->file['uploadDate'] = new UTCDateTime();

        try {
            $this->collectionWrapper->insertFile($this->file);
        } catch (DriverRuntimeException $e) {
            $this->abort();

            throw $e;
        }
    }

    private function insertChunkFromBuffer(): void
    {
        if (strlen($this->buffer) == 0) {
            return;
        }

        $data = $this->buffer;
        $this->buffer = '';

        $chunk = [
            'files_id' => $this->file['_id'],
            'n' => $this->chunkOffset,
            'data' => new Binary($data),
        ];

        try {
            $this->collectionWrapper->insertChunk($chunk);
        } catch (DriverRuntimeException $e) {
            $this->abort();

            throw $e;
        }

        $this->length += strlen($data);
        $this->chunkOffset++;
    }
}
