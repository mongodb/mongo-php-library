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
}
