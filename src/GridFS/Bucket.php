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

use MongoDB\BSON\Document;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Collection;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\GridFS\Exception\CorruptFileException;
use MongoDB\GridFS\Exception\FileNotFoundException;
use MongoDB\GridFS\Exception\LogicException;
use MongoDB\GridFS\Exception\StreamException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\Find;

use function array_intersect_key;
use function array_key_exists;
use function assert;
use function explode;
use function fopen;
use function get_resource_type;
use function in_array;
use function is_array;
use function is_integer;
use function is_object;
use function is_resource;
use function is_string;
use function method_exists;
use function MongoDB\apply_type_map_to_document;
use function property_exists;
use function sprintf;
use function str_contains;
use function stream_context_create;
use function stream_copy_to_stream;
use function stream_get_meta_data;
use function stream_get_wrappers;
use function urlencode;

/**
 * Bucket provides a public API for interacting with the GridFS files and chunks
 * collections.
 */
class Bucket
{
    private const DEFAULT_BUCKET_NAME = 'fs';

    private const DEFAULT_CHUNK_SIZE_BYTES = 261120;

    private const DEFAULT_TYPE_MAP = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    private const STREAM_WRAPPER_PROTOCOL = 'gridfs';

    private ?DocumentCodec $codec = null;

    private CollectionWrapper $collectionWrapper;

    private string $bucketName;

    private int $chunkSizeBytes;

    private ReadConcern $readConcern;

    private ReadPreference $readPreference;

    private array $typeMap;

    private WriteConcern $writeConcern;

    /**
     * Constructs a GridFS bucket.
     *
     * Supported options:
     *
     *  * bucketName (string): The bucket name, which will be used as a prefix
     *    for the files and chunks collections. Defaults to "fs".
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to
     *    261120 (i.e. 255 KiB).
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Bucket options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private Manager $manager, private string $databaseName, array $options = [])
    {
        $options += [
            'bucketName' => self::DEFAULT_BUCKET_NAME,
            'chunkSizeBytes' => self::DEFAULT_CHUNK_SIZE_BYTES,
        ];

        if (! is_string($options['bucketName'])) {
            throw InvalidArgumentException::invalidType('"bucketName" option', $options['bucketName'], 'string');
        }

        if (! is_integer($options['chunkSizeBytes'])) {
            throw InvalidArgumentException::invalidType('"chunkSizeBytes" option', $options['chunkSizeBytes'], 'integer');
        }

        if ($options['chunkSizeBytes'] < 1) {
            throw new InvalidArgumentException(sprintf('Expected "chunkSizeBytes" option to be >= 1, %d given', $options['chunkSizeBytes']));
        }

        if (isset($options['codec']) && ! $options['codec'] instanceof DocumentCodec) {
            throw InvalidArgumentException::invalidType('"codec" option', $options['codec'], DocumentCodec::class);
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['codec']) && isset($options['typeMap'])) {
            throw InvalidArgumentException::cannotCombineCodecAndTypeMap();
        }

        $this->bucketName = $options['bucketName'];
        $this->chunkSizeBytes = $options['chunkSizeBytes'];
        $this->codec = $options['codec'] ?? null;
        $this->readConcern = $options['readConcern'] ?? $this->manager->getReadConcern();
        $this->readPreference = $options['readPreference'] ?? $this->manager->getReadPreference();
        $this->typeMap = $options['typeMap'] ?? self::DEFAULT_TYPE_MAP;
        $this->writeConcern = $options['writeConcern'] ?? $this->manager->getWriteConcern();

        /* The codec option is intentionally omitted when constructing the files
         * and chunks collections so as not to interfere with internal GridFS
         * operations. Any codec will be manually applied when querying the
         * files collection (i.e. find, findOne, and getFileDocumentForStream).
         */
        $collectionOptions = array_intersect_key($options, ['readConcern' => 1, 'readPreference' => 1, 'typeMap' => 1, 'writeConcern' => 1]);

        $this->collectionWrapper = new CollectionWrapper($manager, $databaseName, $options['bucketName'], $collectionOptions);
        $this->registerStreamWrapper();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return [
            'bucketName' => $this->bucketName,
            'codec' => $this->codec,
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'chunkSizeBytes' => $this->chunkSizeBytes,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Delete a file from the GridFS bucket.
     *
     * If the files collection document is not found, this method will still
     * attempt to delete orphaned chunks.
     *
     * @param mixed $id File ID
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function delete(mixed $id): void
    {
        $file = $this->collectionWrapper->findFileById($id);
        $this->collectionWrapper->deleteFileAndChunksById($id);

        if ($file === null) {
            throw FileNotFoundException::byId($id, $this->getFilesNamespace());
        }
    }

    /**
     * Delete all the revisions of a file name from the GridFS bucket.
     *
     * @param string $filename Filename
     *
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function deleteByName(string $filename): void
    {
        $count = $this->collectionWrapper->deleteFileAndChunksByFilename($filename);

        if ($count === 0) {
            throw FileNotFoundException::byFilename($filename);
        }
    }

    /**
     * Writes the contents of a GridFS file to a writable stream.
     *
     * @param mixed    $id          File ID
     * @param resource $destination Writable Stream
     * @throws FileNotFoundException if no file could be selected
     * @throws InvalidArgumentException if $destination is not a stream
     * @throws StreamException if the file could not be uploaded
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function downloadToStream(mixed $id, $destination): void
    {
        if (! is_resource($destination) || get_resource_type($destination) != 'stream') {
            throw InvalidArgumentException::invalidType('$destination', $destination, 'resource');
        }

        $source = $this->openDownloadStream($id);
        if (@stream_copy_to_stream($source, $destination) === false) {
            throw StreamException::downloadFromIdFailed($id, $source, $destination);
        }
    }

    /**
     * Writes the contents of a GridFS file, which is selected by name and
     * revision, to a writable stream.
     *
     * Supported options:
     *
     *  * revision (integer): Which revision (i.e. documents with the same
     *    filename and different uploadDate) of the file to retrieve. Defaults
     *    to -1 (i.e. the most recent revision).
     *
     * Revision numbers are defined as follows:
     *
     *  * 0 = the original stored file
     *  * 1 = the first revision
     *  * 2 = the second revision
     *  * etc…
     *  * -2 = the second most recent revision
     *  * -1 = the most recent revision
     *
     * @param string   $filename    Filename
     * @param resource $destination Writable Stream
     * @param array    $options     Download options
     * @throws FileNotFoundException if no file could be selected
     * @throws InvalidArgumentException if $destination is not a stream
     * @throws StreamException if the file could not be uploaded
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function downloadToStreamByName(string $filename, $destination, array $options = []): void
    {
        if (! is_resource($destination) || get_resource_type($destination) != 'stream') {
            throw InvalidArgumentException::invalidType('$destination', $destination, 'resource');
        }

        $source = $this->openDownloadStreamByName($filename, $options);
        if (@stream_copy_to_stream($source, $destination) === false) {
            throw StreamException::downloadFromFilenameFailed($filename, $source, $destination);
        }
    }

    /**
     * Drops the files and chunks collections associated with this GridFS
     * bucket.
     *
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function drop(): void
    {
        $this->collectionWrapper->dropCollections();
    }

    /**
     * Finds documents from the GridFS bucket's files collection matching the
     * query.
     *
     * @see Find::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function find(array|object $filter = [], array $options = []): CursorInterface
    {
        if ($this->codec && ! array_key_exists('codec', $options)) {
            $options['codec'] = $this->codec;
        }

        return $this->collectionWrapper->findFiles($filter, $options);
    }

    /**
     * Finds a single document from the GridFS bucket's files collection
     * matching the query.
     *
     * @see FindOne::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function findOne(array|object $filter = [], array $options = []): array|object|null
    {
        if ($this->codec && ! array_key_exists('codec', $options)) {
            $options['codec'] = $this->codec;
        }

        return $this->collectionWrapper->findOneFile($filter, $options);
    }

    /**
     * Return the bucket name.
     */
    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    /**
     * Return the chunks collection.
     */
    public function getChunksCollection(): Collection
    {
        return $this->collectionWrapper->getChunksCollection();
    }

    /**
     * Return the chunk size in bytes.
     */
    public function getChunkSizeBytes(): int
    {
        return $this->chunkSizeBytes;
    }

    /**
     * Return the database name.
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * Gets the file document of the GridFS file associated with a stream.
     *
     * @param resource $stream GridFS stream
     * @throws InvalidArgumentException if $stream is not a GridFS stream
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function getFileDocumentForStream($stream): array|object
    {
        $file = $this->getRawFileDocumentForStream($stream);

        if ($this->codec) {
            return $this->codec->decode(Document::fromPHP($file));
        }

        // Filter the raw document through the specified type map
        return apply_type_map_to_document($file, $this->typeMap);
    }

    /**
     * Gets the file document's ID of the GridFS file associated with a stream.
     *
     * @param resource $stream GridFS stream
     * @throws CorruptFileException if the file "_id" field does not exist
     * @throws InvalidArgumentException if $stream is not a GridFS stream
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function getFileIdForStream($stream): mixed
    {
        $file = $this->getRawFileDocumentForStream($stream);

        /* Filter the raw document through the specified type map, but override
         * the root type so we can reliably access the ID.
         */
        $typeMap = ['root' => 'stdClass'] + $this->typeMap;
        $file = apply_type_map_to_document($file, $typeMap);
        assert(is_object($file));

        if (! isset($file->_id) && ! property_exists($file, '_id')) {
            throw new CorruptFileException('file._id does not exist');
        }

        return $file->_id;
    }

    /**
     * Return the files collection.
     */
    public function getFilesCollection(): Collection
    {
        return $this->collectionWrapper->getFilesCollection();
    }

    /**
     * Return the read concern for this GridFS bucket.
     *
     * @see https://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     */
    public function getReadConcern(): ReadConcern
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this GridFS bucket.
     */
    public function getReadPreference(): ReadPreference
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this GridFS bucket.
     */
    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this GridFS bucket.
     *
     * @see https://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     */
    public function getWriteConcern(): WriteConcern
    {
        return $this->writeConcern;
    }

    /**
     * Opens a readable stream for reading a GridFS file.
     *
     * @param mixed $id File ID
     * @return resource
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function openDownloadStream(mixed $id)
    {
        $file = $this->collectionWrapper->findFileById($id);

        if ($file === null) {
            throw FileNotFoundException::byId($id, $this->getFilesNamespace());
        }

        return $this->openDownloadStreamByFile($file);
    }

    /**
     * Opens a readable stream to read a GridFS file, which is selected
     * by name and revision.
     *
     * Supported options:
     *
     *  * revision (integer): Which revision (i.e. documents with the same
     *    filename and different uploadDate) of the file to retrieve. Defaults
     *    to -1 (i.e. the most recent revision).
     *
     * Revision numbers are defined as follows:
     *
     *  * 0 = the original stored file
     *  * 1 = the first revision
     *  * 2 = the second revision
     *  * etc…
     *  * -2 = the second most recent revision
     *  * -1 = the most recent revision
     *
     * @param string $filename Filename
     * @param array  $options  Download options
     * @return resource
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function openDownloadStreamByName(string $filename, array $options = [])
    {
        $options += ['revision' => -1];

        $file = $this->collectionWrapper->findFileByFilenameAndRevision($filename, $options['revision']);

        if ($file === null) {
            throw FileNotFoundException::byFilenameAndRevision($filename, $options['revision'], $this->getFilesNamespace());
        }

        return $this->openDownloadStreamByFile($file);
    }

    /**
     * Opens a writable stream for writing a GridFS file.
     *
     * Supported options:
     *
     *  * _id (mixed): File document identifier. Defaults to a new ObjectId.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to the
     *    bucket's chunk size.
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param string $filename Filename
     * @param array  $options  Upload options
     * @return resource
     */
    public function openUploadStream(string $filename, array $options = [])
    {
        $options += [
            'chunkSizeBytes' => $this->chunkSizeBytes,
        ];

        $path = $this->createPathForUpload();
        $context = stream_context_create([
            self::STREAM_WRAPPER_PROTOCOL => [
                'collectionWrapper' => $this->collectionWrapper,
                'filename' => $filename,
                'options' => $options,
            ],
        ]);

        return fopen($path, 'w', false, $context);
    }

    /**
     * Register an alias to enable basic filename access for this bucket.
     *
     * For applications that need to interact with GridFS using only a filename
     * string, a bucket can be registered with an alias. Files can then be
     * accessed using the following pattern:
     *
     *     gridfs://<bucket-alias>/<filename>
     *
     * Read operations will always target the most recent revision of a file.
     *
     * @param non-empty-string string $alias The alias to use for the bucket
     */
    public function registerGlobalStreamWrapperAlias(string $alias): void
    {
        if ($alias === '' || str_contains($alias, '/')) {
            throw new InvalidArgumentException(sprintf('The bucket alias must be a non-empty string without any slash, "%s" given', $alias));
        }

        // Use a closure to expose the private method into another class
        StreamWrapper::setContextResolver($alias, fn (string $path, string $mode, array $context) => $this->resolveStreamContext($path, $mode, $context));
    }

    /**
     * Renames the GridFS file with the specified ID.
     *
     * @param mixed  $id          File ID
     * @param string $newFilename New filename
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function rename(mixed $id, string $newFilename): void
    {
        $updateResult = $this->collectionWrapper->updateFilenameForId($id, $newFilename);

        if ($updateResult->getModifiedCount() === 1) {
            return;
        }

        // If the update resulted in no modification, it's possible that the
        // file did not exist, in which case we must raise an error.
        if ($updateResult->getMatchedCount() !== 1) {
            throw FileNotFoundException::byId($id, $this->getFilesNamespace());
        }
    }

    /**
     * Renames all the revisions of a file name in the GridFS bucket.
     *
     * @param string $filename    Filename
     * @param string $newFilename New filename
     *
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function renameByName(string $filename, string $newFilename): void
    {
        $count = $this->collectionWrapper->updateFilenameForFilename($filename, $newFilename);

        if ($count === 0) {
            throw FileNotFoundException::byFilename($filename);
        }
    }

    /**
     * Writes the contents of a readable stream to a GridFS file.
     *
     * Supported options:
     *
     *  * _id (mixed): File document identifier. Defaults to a new ObjectId.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to the
     *    bucket's chunk size.
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param string   $filename Filename
     * @param resource $source   Readable stream
     * @param array    $options  Stream options
     * @return mixed ID of the newly created GridFS file
     * @throws InvalidArgumentException if $source is not a GridFS stream
     * @throws StreamException if the file could not be uploaded
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function uploadFromStream(string $filename, $source, array $options = []): mixed
    {
        if (! is_resource($source) || get_resource_type($source) != 'stream') {
            throw InvalidArgumentException::invalidType('$source', $source, 'resource');
        }

        $destination = $this->openUploadStream($filename, $options);

        if (@stream_copy_to_stream($source, $destination) === false) {
            $destinationUri = $this->createPathForFile($this->getRawFileDocumentForStream($destination));

            throw StreamException::uploadFailed($filename, $source, $destinationUri);
        }

        return $this->getFileIdForStream($destination);
    }

    /**
     * Creates a path for an existing GridFS file.
     *
     * @param object $file GridFS file document
     */
    private function createPathForFile(object $file): string
    {
        if (is_array($file->_id) || (is_object($file->_id) && ! method_exists($file->_id, '__toString'))) {
            $id = Document::fromPHP(['_id' => $file->_id])->toRelaxedExtendedJSON();
        } else {
            $id = (string) $file->_id;
        }

        return sprintf(
            '%s://%s/%s.files/%s',
            self::STREAM_WRAPPER_PROTOCOL,
            urlencode($this->databaseName),
            urlencode($this->bucketName),
            urlencode($id),
        );
    }

    /**
     * Creates a path for a new GridFS file, which does not yet have an ID.
     */
    private function createPathForUpload(): string
    {
        return sprintf(
            '%s://%s/%s.files',
            self::STREAM_WRAPPER_PROTOCOL,
            urlencode($this->databaseName),
            urlencode($this->bucketName),
        );
    }

    /**
     * Returns the names of the files collection.
     */
    private function getFilesNamespace(): string
    {
        return sprintf('%s.%s.files', $this->databaseName, $this->bucketName);
    }

    /**
     * Gets the file document of the GridFS file associated with a stream.
     *
     * This returns the raw document from the StreamWrapper, which does not
     * respect the Bucket's type map.
     *
     * @param resource $stream GridFS stream
     * @throws InvalidArgumentException
     */
    private function getRawFileDocumentForStream($stream): object
    {
        if (! is_resource($stream) || get_resource_type($stream) != 'stream') {
            throw InvalidArgumentException::invalidType('$stream', $stream, 'resource');
        }

        $metadata = stream_get_meta_data($stream);

        if (! isset($metadata['wrapper_data']) || ! $metadata['wrapper_data'] instanceof StreamWrapper) {
            throw InvalidArgumentException::invalidType('$stream wrapper data', $metadata['wrapper_data'] ?? null, StreamWrapper::class);
        }

        return $metadata['wrapper_data']->getFile();
    }

    /**
     * Opens a readable stream for the GridFS file.
     *
     * @param object $file GridFS file document
     * @return resource
     */
    private function openDownloadStreamByFile(object $file)
    {
        $path = $this->createPathForFile($file);
        $context = stream_context_create([
            self::STREAM_WRAPPER_PROTOCOL => [
                'collectionWrapper' => $this->collectionWrapper,
                'file' => $file,
            ],
        ]);

        return fopen($path, 'r', false, $context);
    }

    /**
     * Registers the GridFS stream wrapper if it is not already registered.
     */
    private function registerStreamWrapper(): void
    {
        if (in_array(self::STREAM_WRAPPER_PROTOCOL, stream_get_wrappers())) {
            return;
        }

        StreamWrapper::register(self::STREAM_WRAPPER_PROTOCOL);
    }

    /**
     * Create a stream context from the path and mode provided to fopen().
     *
     * @see StreamWrapper::setContextResolver()
     *
     * @param string                                      $path    The full url provided to fopen(). It contains the filename.
     *                                                             gridfs://database_name/collection_name.files/file_name
     * @param array{revision?: int, chunkSizeBytes?: int} $context The options provided to fopen()
     *
     * @return array{collectionWrapper: CollectionWrapper, file: object}|array{collectionWrapper: CollectionWrapper, filename: string, options: array}
     *
     * @throws FileNotFoundException
     * @throws LogicException
     */
    private function resolveStreamContext(string $path, string $mode, array $context): array
    {
        // Fallback to an empty filename if the path does not contain one: "gridfs://alias"
        $filename = explode('/', $path, 4)[3] ?? '';

        if ($mode === 'r' || $mode === 'rb') {
            $file = $this->collectionWrapper->findFileByFilenameAndRevision($filename, $context['revision'] ?? -1);

            if (! is_object($file)) {
                throw FileNotFoundException::byFilenameAndRevision($filename, $context['revision'] ?? -1, $path);
            }

            return [
                'collectionWrapper' => $this->collectionWrapper,
                'file' => $file,
            ];
        }

        if ($mode === 'w' || $mode === 'wb') {
            return [
                'collectionWrapper' => $this->collectionWrapper,
                'filename' => $filename,
                'options' => $context + [
                    'chunkSizeBytes' => $this->chunkSizeBytes,
                ],
            ];
        }

        throw LogicException::openModeNotSupported($mode);
    }
}
