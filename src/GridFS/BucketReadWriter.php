<?php
namespace MongoDB\GridFS;

class BucketReadWriter
{
    private $bucket;

    public function __construct(Bucket $bucket)
    {
        $this->bucket = $bucket;
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
        $options = [
            'bucket' => $this->bucket,
            'uploadOptions' => $options
            ];
        $context = stream_context_create(['gridfs' => $options]);
        return fopen(sprintf('gridfs://%s/%s', $this->bucket->getDatabaseName(), $filename), 'w', false, $context);
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
        $gridFsStream = new GridFsUpload($this->bucket, $filename, $options);
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
        $options = [
            'bucket' => $this->bucket
            ];
        $context = stream_context_create(['gridfs' => $options]);
        return fopen(sprintf('gridfs://%s/%s', $this->bucket->getDatabaseName(), $id), 'r', false, $context);
    }
      /**
       * Downloads the contents of the stored file specified by id and writes
       * the contents to the destination Stream.
       * @param ObjectId  $id           GridFS File Id
       * @param Stream    $destination  Destination Stream
       */
    public function downloadToStream(\MongoDB\BSON\ObjectId $id, $destination)
    {
        $gridFsStream = new GridFsDownload($this->bucket, $id);
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
        $options =[];
        $writeConcern = $this->bucket->getWriteConcern();
        if(!is_null($writeConcern)) {
            $options['writeConcern'] = $writeConcern;
        }
        $file = $this->bucket->getFilesCollection()->findOne(['_id' => $id]);
        $this->bucket->getChunksCollection()->deleteMany(['files_id' => $id], $options);
        if (is_null($file)) {
            throw new \MongoDB\Exception\GridFSFileNotFoundException($id, $this->bucket->getDatabaseName(), $this->bucket->getBucketName());
        }

        $this->bucket->getFilesCollection()->deleteOne(['_id' => $id], $options);
    }
    /**
    * Open a stream to download a file from the GridFS bucket. Searches for the file by the specified name then returns a stream to the specified file
    * @param string    $filename     name of the file to download
    * @param int       $revision     the revision of the file to download
    * @throws GridFSFileNotFoundException
    */
    public function openDownloadStreamByName($filename, $revision = -1)
    {
        $file = $this->bucket->findFileRevision($filename, $revision);
        $options = ['bucket' => $this->bucket,
                    'file' => $file
                ];
        $context = stream_context_create(['gridfs' => $options]);
        return fopen(sprintf('gridfs://%s/%s', $this->bucket->getDatabaseName(), $filename), 'r', false, $context);
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
        $file = $this->bucket->findFileRevision($filename, $revision);
        $gridFsStream = new GridFsDownload($this->bucket, null, $file);
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
        return $this->bucket->getFilesCollection()->find($filter, $options);
    }
}
