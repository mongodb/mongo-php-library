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

use Closure;
use MongoDB\BSON\UTCDateTime;
use MongoDB\GridFS\Exception\FileNotFoundException;
use MongoDB\GridFS\Exception\LogicException;

use function array_slice;
use function assert;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function is_integer;
use function is_resource;
use function str_starts_with;
use function stream_context_get_options;
use function stream_get_wrappers;
use function stream_wrapper_register;
use function stream_wrapper_unregister;

use const SEEK_CUR;
use const SEEK_END;
use const SEEK_SET;
use const STREAM_IS_URL;

/**
 * Stream wrapper for reading and writing a GridFS file.
 *
 * @internal
 * @see Bucket::openUploadStream()
 * @see Bucket::openDownloadStream()
 * @psalm-type ContextOptions = array{collectionWrapper: CollectionWrapper, file: object}|array{collectionWrapper: CollectionWrapper, filename: string, options: array}
 */
class StreamWrapper
{
    /** @var resource|null Stream context (set by PHP) */
    public $context;

    private ReadableStream|WritableStream|null $stream = null;

    /** @var array<string, Closure(string, string, array): ContextOptions> */
    private static array $contextResolvers = [];

    public function __destruct()
    {
        /* Ensure the stream is closed so the last chunk is written. This is
         * necessary because PHP would close the stream after all objects have
         * been destructed. This can cause autoloading issues and possibly
         * segmentation faults during PHP shutdown. */
        $this->stream_close();
    }

    /**
     * Return the stream's file document.
     */
    public function getFile(): object
    {
        assert($this->stream !== null);

        return $this->stream->getFile();
    }

    /**
     * Register the GridFS stream wrapper.
     *
     * @param string $protocol Protocol to use for stream_wrapper_register()
     */
    public static function register(string $protocol = 'gridfs'): void
    {
        if (in_array($protocol, stream_get_wrappers())) {
            stream_wrapper_unregister($protocol);
        }

        stream_wrapper_register($protocol, static::class, STREAM_IS_URL);
    }

    /**
     * Rename all revisions of a filename.
     *
     * @return true
     * @throws FileNotFoundException
     */
    public function rename(string $fromPath, string $toPath): bool
    {
        $prefix = implode('/', array_slice(explode('/', $fromPath, 4), 0, 3)) . '/';
        if (! str_starts_with($toPath, $prefix)) {
            throw LogicException::renamePathMismatch($fromPath, $toPath);
        }

        $context = $this->getContext($fromPath, 'w');

        $newFilename = explode('/', $toPath, 4)[3] ?? '';
        $count = $context['collectionWrapper']->updateFilenameForFilename($context['filename'], $newFilename);

        if ($count === 0) {
            throw FileNotFoundException::byFilename($fromPath);
        }

        // If $count is null, the update is unacknowledged, the operation is considered successful.
        return true;
    }

    /**
     * @see Bucket::resolveStreamContext()
     *
     * @param Closure(string, string, array):ContextOptions|null $resolver
     */
    public static function setContextResolver(string $name, ?Closure $resolver): void
    {
        if ($resolver === null) {
            unset(self::$contextResolvers[$name]);
        } else {
            self::$contextResolvers[$name] = $resolver;
        }
    }

    /**
     * Closes the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-close.php
     */
    public function stream_close(): void
    {
        if (! $this->stream) {
            return;
        }

        $this->stream->close();
    }

    /**
     * Returns whether the file pointer is at the end of the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-eof.php
     */
    public function stream_eof(): bool
    {
        if (! $this->stream instanceof ReadableStream) {
            return false;
        }

        return $this->stream->isEOF();
    }

    /**
     * Opens the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-open.php
     * @param string      $path       Path to the file resource
     * @param string      $mode       Mode used to open the file (only "r" and "w" are supported)
     * @param integer     $options    Additional flags set by the streams API
     * @param string|null $openedPath Not used
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        if ($mode === 'r' || $mode === 'rb') {
            return $this->initReadableStream($this->getContext($path, $mode));
        }

        if ($mode === 'w' || $mode === 'wb') {
            return $this->initWritableStream($this->getContext($path, $mode));
        }

        throw LogicException::openModeNotSupported($mode);
    }

    /**
     * Read bytes from the stream.
     *
     * Note: this method may return a string smaller than the requested length
     * if data is not available to be read.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-read.php
     * @param integer $length Number of bytes to read
     */
    public function stream_read(int $length): string
    {
        if (! $this->stream instanceof ReadableStream) {
            return '';
        }

        return $this->stream->readBytes($length);
    }

    /**
     * Return the current position of the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-seek.php
     * @param integer $offset Stream offset to seek to
     * @param integer $whence One of SEEK_SET, SEEK_CUR, or SEEK_END
     * @return boolean True if the position was updated and false otherwise
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        assert($this->stream !== null);

        $size = $this->stream->getSize();

        if ($whence === SEEK_CUR) {
            $offset += $this->stream->tell();
        }

        if ($whence === SEEK_END) {
            $offset += $size;
        }

        // WritableStreams are always positioned at the end of the stream
        if ($this->stream instanceof WritableStream) {
            return $offset === $size;
        }

        if ($offset < 0 || $offset > $size) {
            return false;
        }

        $this->stream->seek($offset);

        return true;
    }

    /**
     * Return information about the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-stat.php
     */
    public function stream_stat(): array
    {
        assert($this->stream !== null);

        $stat = $this->getStatTemplate();

        $stat[2] = $stat['mode'] = $this->stream instanceof ReadableStream
            ? 0100444  // S_IFREG & S_IRUSR & S_IRGRP & S_IROTH
            : 0100222; // S_IFREG & S_IWUSR & S_IWGRP & S_IWOTH
        $stat[7] = $stat['size'] = $this->stream->getSize();

        $file = $this->stream->getFile();

        if (isset($file->uploadDate) && $file->uploadDate instanceof UTCDateTime) {
            $timestamp = $file->uploadDate->toDateTime()->getTimestamp();
            $stat[9] = $stat['mtime'] = $timestamp;
            $stat[10] = $stat['ctime'] = $timestamp;
        }

        if (isset($file->chunkSize) && is_integer($file->chunkSize)) {
            $stat[11] = $stat['blksize'] = $file->chunkSize;
        }

        return $stat;
    }

    /**
     * Return the current position of the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-tell.php
     * @return integer The current position of the stream
     */
    public function stream_tell(): int
    {
        assert($this->stream !== null);

        return $this->stream->tell();
    }

    /**
     * Write bytes to the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-write.php
     * @param string $data Data to write
     * @return integer The number of bytes written
     */
    public function stream_write(string $data): int
    {
        if (! $this->stream instanceof WritableStream) {
            return 0;
        }

        return $this->stream->writeBytes($data);
    }

    /**
     * Remove all revisions of a filename.
     *
     * @return true
     * @throws FileNotFoundException
     */
    public function unlink(string $path): bool
    {
        $context = $this->getContext($path, 'w');
        $count = $context['collectionWrapper']->deleteFileAndChunksByFilename($context['filename']);

        if ($count === 0) {
            throw FileNotFoundException::byFilename($path);
        }

        // If $count is null, the update is unacknowledged, the operation is considered successful.
        return true;
    }

    /** @return false|array */
    public function url_stat(string $path, int $flags)
    {
        assert($this->stream === null);

        try {
            $this->stream_open($path, 'r', 0, $openedPath);
        } catch (FileNotFoundException) {
            return false;
        }

        return $this->stream_stat();
    }

    /**
     * @return array{collectionWrapper: CollectionWrapper, file: object}|array{collectionWrapper: CollectionWrapper, filename: string, options: array}
     * @psalm-return ($mode == 'r' or $mode == 'rb' ? array{collectionWrapper: CollectionWrapper, file: object} : array{collectionWrapper: CollectionWrapper, filename: string, options: array})
     */
    private function getContext(string $path, string $mode): array
    {
        $context = [];

        /**
         * The Bucket methods { @see Bucket::openUploadStream() } and { @see Bucket::openDownloadStreamByFile() }
         * always set an internal context. But the context can also be set by the user.
         */
        if (is_resource($this->context)) {
            $context = stream_context_get_options($this->context)['gridfs'] ?? [];

            if (! is_array($context)) {
                throw LogicException::invalidContext($context);
            }
        }

        // When the stream is opened using fopen(), the context is not required, it can contain only options.
        if (! isset($context['collectionWrapper'])) {
            $bucketAlias = explode('/', $path, 4)[2] ?? '';

            if (! isset(self::$contextResolvers[$bucketAlias])) {
                throw LogicException::bucketAliasNotRegistered($bucketAlias);
            }

            /** @see Bucket::resolveStreamContext() */
            $context = self::$contextResolvers[$bucketAlias]($path, $mode, $context);
        }

        if (! $context['collectionWrapper'] instanceof CollectionWrapper) {
            throw LogicException::invalidContextCollectionWrapper($context['collectionWrapper']);
        }

        return $context;
    }

    /**
     * Returns a stat template with default values.
     */
    private function getStatTemplate(): array
    {
        return [
            // phpcs:disable Squiz.Arrays.ArrayDeclaration.IndexNoNewline
            0  => 0,  'dev'     => 0,
            1  => 0,  'ino'     => 0,
            2  => 0,  'mode'    => 0,
            3  => 0,  'nlink'   => 0,
            4  => 0,  'uid'     => 0,
            5  => 0,  'gid'     => 0,
            6  => -1, 'rdev'    => -1,
            7  => 0,  'size'    => 0,
            8  => 0,  'atime'   => 0,
            9  => 0,  'mtime'   => 0,
            10 => 0,  'ctime'   => 0,
            11 => -1, 'blksize' => -1,
            12 => -1, 'blocks'  => -1,
            // phpcs:enable
        ];
    }

    /**
     * Initialize the internal stream for reading.
     *
     * @param array{collectionWrapper: CollectionWrapper, file: object} $contextOptions
     */
    private function initReadableStream(array $contextOptions): bool
    {
        $this->stream = new ReadableStream(
            $contextOptions['collectionWrapper'],
            $contextOptions['file'],
        );

        return true;
    }

    /**
     * Initialize the internal stream for writing.
     *
     * @param array{collectionWrapper: CollectionWrapper, filename: string, options: array} $contextOptions
     */
    private function initWritableStream(array $contextOptions): bool
    {
        $this->stream = new WritableStream(
            $contextOptions['collectionWrapper'],
            $contextOptions['filename'],
            $contextOptions['options'],
        );

        return true;
    }
}
