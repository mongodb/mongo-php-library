<?php
namespace MongoDB\GridFS;

use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedValueException;
/**
 * Bucket abstracts the GridFS files and chunks collections.
 *
 * @api
 */
class Bucket
{
    private $databaseName;
    private $collectionsWrapper;
    private $options;
    private static $streamWrapper;
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
     * @param Manager  $manager      Manager instance from the driver
     * @param string   $databaseName Database name
     * @param array    $options      Bucket options
     * @throws InvalidArgumentException
     */
    public function __construct(Manager $manager, $databaseName, array $options = [])
    {
        $options += [
            'chunkSizeBytes' => 261120,
            'bucketName' => 'fs'
        ];
        $this->databaseName = (string) $databaseName;
        $this->options = $options;
        $this->collectionsWrapper = new GridFSCollectionsWrapper($manager, $databaseName, $options);
        $this->registerStreamWrapper($manager);
    }

    /**
     * Opens a Stream for writing the contents of a file.
     *
     * @param string   $filename     file to upload
     * @param array    $options      Stream Options
     * @return Stream  uploadStream
     */
    public function openUploadStream($filename, array $options = [])
    {
        $options+= ['chunkSizeBytes' => $this->options['chunkSizeBytes']];
        $streamOptions = [
            'collectionsWrapper' => $this->collectionsWrapper,
            'uploadOptions' => $options
            ];
        $context = stream_context_create(['gridfs' => $streamOptions]);
        return fopen(sprintf('gridfs://%s/%s', $this->databaseName, $filename), 'w', false, $context);
    }
    /**
     * Upload a file to this bucket by specifying the source stream file
     *
     * @param String   $filename    Filename To Insert
     * @param Stream   $source      Source Stream
     * @param array    $options     Stream Options
     * @return ObjectId
     */
    public function uploadFromStream($filename, $source, array $options = [])
    {
        $options+= ['chunkSizeBytes' => $this->options['chunkSizeBytes']];
        $gridFsStream = new GridFsUpload($this->collectionsWrapper, $filename, $options);
        return $gridFsStream->uploadFromStream($source);
    }
    /**
     * Opens a Stream for reading the contents of a file specified by ID.
     *
     * @param ObjectId $id
     * @return Stream
     */
    public function openDownloadStream(\MongoDB\BSON\ObjectId $id)
    {
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(['_id' => $id]);
        if (is_null($file)) {
            throw new \MongoDB\Exception\GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }
        return $this->openDownloadStreamByFile($file);
    }
      /**
       * Downloads the contents of the stored file specified by id and writes
       * the contents to the destination Stream.
       * @param ObjectId  $id           GridFS File Id
       * @param Stream    $destination  Destination Stream
       */
    public function downloadToStream(\MongoDB\BSON\ObjectId $id, $destination)
    {
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(['_id' => $id]);
        if (is_null($file)) {
            throw new \MongoDB\Exception\GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }
        $gridFsStream = new GridFsDownload($this->collectionsWrapper, $file);
        $gridFsStream->downloadToStream($destination);
    }
    /**
    * Delete a file from the GridFS bucket. If the file collection entry is not found, still attempts to delete orphaned chunks
    *
    * @param ObjectId    $id     file id
    * @throws GridFSFileNotFoundException
    */
    public function delete(\MongoDB\BSON\ObjectId $id)
    {
        $file = $this->collectionsWrapper->getFilesCollection()->findOne(['_id' => $id]);
        $this->collectionsWrapper->getChunksCollection()->deleteMany(['files_id' => $id]);
        if (is_null($file)) {
            throw new \MongoDB\Exception\GridFSFileNotFoundException($id, $this->collectionsWrapper->getFilesCollection()->getNameSpace());
        }
        $this->collectionsWrapper->getFilesCollection()->deleteOne(['_id' => $id]);
    }
    /**
    * Open a stream to download a file from the GridFS bucket. Searches for the file by the specified name then returns a stream to the specified file
    * @param string    $filename     name of the file to download
    * @param int       $revision     the revision of the file to download
    * @throws GridFSFileNotFoundException
    */
    public function openDownloadStreamByName($filename, $revision = -1)
    {
        $file = $this->findFileRevision($filename, $revision);
        return $this->openDownloadStreamByFile($file);
    }
    /**
    * Download a file from the GridFS bucket by name. Searches for the file by the specified name then loads data into the stream
    *
    * @param string    $filename     name of the file to download
    * @param int       $revision     the revision of the file to download
    * @throws GridFSFileNotFoundException
    */
    public function downloadToStreamByName($filename, $destination, $revision=-1)
    {
        $file = $this->findFileRevision($filename, $revision);
        $gridFsStream = new GridFsDownload($this->collectionsWrapper, $file);
        $gridFsStream->downloadToStream($destination);
    }
    /**
    * Find files from the GridFS bucket files collection.
    *
    * @param array    $filter     filter to find by
    * @param array    $options    options to
    */
    public function find($filter, array $options =[])
    {
        return $this->collectionsWrapper->getFilesCollection()->find($filter, $options);
    }

    public function getIdFromStream($stream)
    {
        $metadata = stream_get_meta_data($stream);
        if(isset($metadata["wrapper_data"]->id)){
            return $metadata["wrapper_data"]->id;
        }
        return null;
    }

    public function getCollectionsWrapper()
    {
        return $this->collectionsWrapper;
    }
    public function getDatabaseName(){
        return $this->databaseName;
    }
    private function openDownloadStreamByFile($file)
    {
        $options = ['collectionsWrapper' => $this->collectionsWrapper,
                    'file' => $file
                ];
        $context = stream_context_create(['gridfs' => $options]);
        return fopen(sprintf('gridfs://%s/%s', $this->databaseName, $file->filename), 'r', false, $context);
    }
    private function findFileRevision($filename, $revision)
    {
        if ($revision < 0) {
            $skip = abs($revision) -1;
            $sortOrder = -1;
        } else {
            $skip = $revision;
            $sortOrder = 1;
        }
        $filesCollection = $this->collectionsWrapper->getFilesCollection();
        $file = $filesCollection->findOne(["filename"=> $filename], ["sort" => ["uploadDate"=> $sortOrder], "limit"=>1, "skip" => $skip]);
        if(is_null($file)) {
            throw new \MongoDB\Exception\GridFSFileNotFoundException($filename, $filesCollection->getNameSpace());
        }
        return $file;
    }
    private function registerStreamWrapper($manager)
    {
        if(isset(Bucket::$streamWrapper)){
            return;
        }
        Bucket::$streamWrapper = new \MongoDB\GridFS\StreamWrapper();
        Bucket::$streamWrapper->register($manager);
    }
}
