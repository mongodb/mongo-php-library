<?php

namespace MongoDB\GridFS;

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\GridFSFileNotFoundException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Find;

/**
 * Bucket provides a public API for interacting with the GridFS files and chunks
 * collections.
 *
 * @api
 */
class Bucket
{
    private static $streamWrapper;

    private $collectionsWrapper;
    private $databaseName;
    private $options;

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
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Bucket options
     * @throws InvalidArgumentException
     */
    public function __construct(Manager $manager, $databaseName, array $options = [])
    {
        $options += [
            'bucketName' => 'fs',
            'chunkSizeBytes' => 261120,
        ];

        if (isset($options['bucketName']) && ! is_string($options['bucketName'])) {
            throw InvalidArgumentException::invalidType('"bucketName" option', $options['bucketName'], 'string');
        }

        if (isset($options['chunkSizeBytes']) && ! is_integer($options['chunkSizeBytes'])) {
            throw InvalidArgumentException::invalidType('"chunkSizeBytes" option', $options['chunkSizeBytes'], 'integer');
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->databaseName = (string) $databaseName;
        $this->options = $options;

        $collectionOptions = array_intersect_key($options, ['readPreference' => 1, 'writeConcern' => 1]);

        $this->collectionsWrapper = new GridFSCollectionsWrapper($manager, $databaseName, $options['bucketName'], $collectionOptions);
        $this->registerStreamWrapper($manager);
    }

    /**
     * Delete a file from the GridFS bucket.
     *
     * If the files collection document is not found, this method will still
     * attempt to delete orphaned chunks.
     *
     * @param ObjectId $id ObjectId of the file
     * @throws GridFSFileNotFoundException
     */
    public function delete(ObjectId $id)
    {
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(['_id' => $id]);
        $this->collectionsWrapper->getFilesCollection()->deleteOne(['_id' => $id]);
        $this->collectionsWrapper->getChunksCollection()->deleteMany(['files_id' => $id]);

        if ($file === null) {
            throw new GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }

    }

    /**
     * Writes the contents of a GridFS file to a writable stream.
     *
     * @param ObjectId $id          ObjectId of the file
     * @param resource $destination Writable Stream
     * @throws GridFSFileNotFoundException
     */
    public function downloadToStream(ObjectId $id, $destination)
    {
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(
            ['_id' => $id],
            ['typeMap' => ['root' => 'stdClass']]
        );

        if ($file === null) {
            throw new GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }

        $gridFsStream = new GridFSDownload($this->collectionsWrapper, $file);
        $gridFsStream->downloadToStream($destination);
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
     * @param string   $filename    File name
     * @param resource $destination Writable Stream
     * @param array    $options     Download options
     * @throws GridFSFileNotFoundException
     */
    public function downloadToStreamByName($filename, $destination, array $options = [])
    {
        $options += ['revision' => -1];
        $file = $this->findFileRevision($filename, $options['revision']);
        $gridFsStream = new GridFSDownload($this->collectionsWrapper, $file);
        $gridFsStream->downloadToStream($destination);
    }

    /**
    * Drops the files and chunks collection associated with GridFS this bucket
    *
    */

    public function drop()
    {
        $this->collectionsWrapper->dropCollections();
    }

    /**
     * Find files from the GridFS bucket's files collection.
     *
     * @see Find::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return Cursor
     */
    public function find($filter, array $options = [])
    {
        return $this->collectionsWrapper->getFilesCollection()->find($filter, $options);
    }

    public function getCollectionsWrapper()
    {
        return $this->collectionsWrapper;
    }

    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Gets the ID of the GridFS file associated with a stream.
     *
     * @param resource $stream GridFS stream
     * @return mixed
     */
    public function getIdFromStream($stream)
    {
        $metadata = stream_get_meta_data($stream);

        if ($metadata['wrapper_data'] instanceof StreamWrapper) {
            return $metadata['wrapper_data']->getId();
        }

        return;
    }

    /**
     * Opens a readable stream for reading a GridFS file.
     *
     * @param ObjectId $id ObjectId of the file
     * @return resource
     * @throws GridFSFileNotFoundException
     */
    public function openDownloadStream(ObjectId $id)
    {
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(
            ['_id' => $id],
            ['typeMap' => ['root' => 'stdClass']]
        );

        if ($file === null) {
            throw new GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }

        return $this->openDownloadStreamByFile($file);
    }

    /**
     * Opens a readable stream stream to read a GridFS file, which is selected
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
     * @param string $filename File name
     * @param array  $options  Download options
     * @return resource
     * @throws GridFSFileNotFoundException
     */
    public function openDownloadStreamByName($filename, array $options = [])
    {
        $options += ['revision' => -1];
        $file = $this->findFileRevision($filename, $options['revision']);

        return $this->openDownloadStreamByFile($file);
    }

    /**
     * Opens a writable stream for writing a GridFS file.
     *
     * Supported options:
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to the
     *    bucket's chunk size.
     *
     * @param string $filename File name
     * @param array  $options  Stream options
     * @return resource
     */
    public function openUploadStream($filename, array $options = [])
    {
        $options += ['chunkSizeBytes' => $this->options['chunkSizeBytes']];

        $streamOptions = [
            'collectionsWrapper' => $this->collectionsWrapper,
            'uploadOptions' => $options,
        ];

        $context = stream_context_create(['gridfs' => $streamOptions]);

        return fopen(sprintf('gridfs://%s/%s', $this->databaseName, $filename), 'w', false, $context);
    }

    /**
     * Renames the GridFS file with the specified ID.
     *
     * @param ObjectId $id          ID of the file to rename
     * @param string   $newFilename New file name
     * @throws GridFSFileNotFoundException
     */
    public function rename(ObjectId $id, $newFilename)
    {
        $filesCollection = $this->collectionsWrapper->getFilesCollection();
        $result = $filesCollection->updateOne(['_id' => $id], ['$set' => ['filename' => $newFilename]]);
        if($result->getModifiedCount() == 0) {
            throw new GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }
    }

    /**
     * Writes the contents of a readable stream to a GridFS file.
     *
     * Supported options:
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to the
     *    bucket's chunk size.
     *
     * @param string   $filename File name
     * @param resource $source   Readable stream
     * @param array    $options  Stream options
     * @return ObjectId
     */
    public function uploadFromStream($filename, $source, array $options = [])
    {
        $options += ['chunkSizeBytes' => $this->options['chunkSizeBytes']];
        $gridFsStream = new GridFSUpload($this->collectionsWrapper, $filename, $options);

        return $gridFsStream->uploadFromStream($source);
    }

    private function findFileRevision($filename, $revision)
    {
        if ($revision < 0) {
            $skip = abs($revision) - 1;
            $sortOrder = -1;
        } else {
            $skip = $revision;
            $sortOrder = 1;
        }

        $filesCollection = $this->collectionsWrapper->getFilesCollection();
        $file = $filesCollection->findOne(
            ['filename' => $filename],
            [
                'skip' => $skip,
                'sort' => ['uploadDate' => $sortOrder],
                'typeMap' => ['root' => 'stdClass'],
            ]
        );

        if ($file === null) {
            throw new GridFSFileNotFoundException($filename, $filesCollection->getNameSpace());
        }

        return $file;
    }

    private function openDownloadStreamByFile($file)
    {
        $options = [
            'collectionsWrapper' => $this->collectionsWrapper,
            'file' => $file,
        ];

        $context = stream_context_create(['gridfs' => $options]);

        return fopen(sprintf('gridfs://%s/%s', $this->databaseName, $file->filename), 'r', false, $context);
    }

    private function registerStreamWrapper(Manager $manager)
    {
        if (isset(self::$streamWrapper)) {
            return;
        }

        self::$streamWrapper = new StreamWrapper();
        self::$streamWrapper->register($manager);
    }
}
