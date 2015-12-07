<?php
namespace MongoDB\GridFS;

use MongoDB\Collection;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedTypeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\BSON;
/**
 * GridFsupload abstracts the processes of inserting into a GridFSBucket
 *
 * @api
 */
class GridFsUpload extends GridFsStream
{
    private $ctx;
    private $bufferLength;
    private $indexChecker;
    private $length=0;
    /**
     * Constructs a GridFS upload stream
     *
     * Supported options:
     *
     *  * contentType (string): DEPRECATED content type to be stored with the file.
     *    This information should now be added to the metadata
     *
     *  * aliases (array of strings): DEPRECATED An array of aliases.
     *    Applications wishing to store aliases should add an aliases field to the
     *    metadata document instead.
     *
     *  * metadata (array or object): User data for the 'metadata' field of the files
     *    collection document.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param \MongoDB\Collection    $filesCollection  Files Collection
     * @param \MongoDB\Collection    $chunksCollection Chunks Collection
     * @param int32                  $chunkSizeBytes   Size of chunk
     * @param string                 $filename         Filename to insert
     * @param array                  $options          File options
     * @throws InvalidArgumentException
     */
    public function __construct(
        Bucket $bucket,
        $filename,
        array $options=[]
        )
    {
        $this->bufferLength = 0;
        $this->ctx = hash_init('md5');

        $uploadDate = time();
        $objectId = new \MongoDB\BSON\ObjectId();
        $main_file = [
            "chunkSize" => $bucket->getChunkSizeBytes(),
            "filename" => $filename,
            "uploadDate" => $uploadDate,
            "_id" => $objectId
        ];

        $fileOptions = [];
        if (isset($options['contentType'])) {
            if (is_string($options['contentType'])) {
                $fileOptions['contentType'] = $options['contentType'];
            } else {
                throw new InvalidArgumentTypeException('"contentType" option', $options['contentType'], 'string');
            }
        }
        if (isset($options['aliases'])) {
            if (\MongoDB\is_string_array($options['aliases'])) {
                $fileOptions['aliases'] = $options['aliases'];
            } else {
                throw new InvalidArgumentTypeException('"aliases" option', $options['aliases'], 'array of strings');
            }
        }

        if (isset($options['metadata'])) {
            if (is_array($options['metadata']) || is_object($options['metadata'])) {
                $fileOptions['metadata'] = $options['metadata'];
            } else {
                throw new InvalidArgumentTypeException('"metadata" option', $options['metadata'], 'object or array');
            }
        }
        $this->file = array_merge($main_file, $fileOptions);
        parent::__construct($bucket);
    }
    /**
    * Reads data from a stream into GridFS
    *
    * @param Stream   $source   Source Stream
    * @return ObjectId
    */
    public function uploadFromStream($source)
    {
        $this->bucket->ensureIndexes();

        if (!is_resource($source) || get_resource_type($source) != "stream") {
            throw new UnexpectedTypeException('stream', $source);
        } else{
            $streamMetadata = stream_get_meta_data($source);
        } if (!is_readable($streamMetadata['uri'])) {
     //       throw new InvalidArgumentException("stream not readable");
            //issue being that php's is_readable reports native streams as not readable like php://temp
        }
        while ($data = fread($source, $this->bucket->getChunkSizeBytes())) {
            $this->insertChunk($data);
        }
        return $this->fileCollectionInsert();
    }
    /**
    * Insert a chunks into GridFS from a string
    *
    * @param string  $toWrite   String to upload
    * @return int
    */
    public function insertChunks($toWrite)
    {
        $this->bucket->ensureIndexes();

        $readBytes = 0;
        while($readBytes != strlen($toWrite)) {
            $addToBuffer = substr($toWrite, $readBytes, $this->bucket->getChunkSizeBytes() - $this->bufferLength);
            fwrite($this->buffer, $addToBuffer);
            $readBytes += strlen($addToBuffer);
            $this->bufferLength += strlen($addToBuffer);
            if($this->bufferLength == $this->bucket->getChunkSizeBytes()) {
                rewind($this->buffer);
                $this->insertChunk(fread($this->buffer, $this->bucket->getChunkSizeBytes()));
                ftruncate($this->buffer,0);
                $this->bufferLength = 0;
            }
        }
        return $readBytes;
    }
    /**
    * Close an active stream, pushes all buffered data to GridFS
    *
    */
    public function close()
    {
        rewind($this->buffer);
        $cached = fread($this->buffer, $this->bucket->getChunkSizeBytes());

        if(strlen($cached) > 0) {
            insertChunk($cached);
        }

        fclose($this->buffer);

        $this->fileCollectionInsert();
    }
    private function insertChunk($data)
    {
        $toUpload = ["files_id" => $this->file['_id'], "n" => $this->n, "data" => new \MongoDB\BSON\Binary($data, \MongoDB\BSON\Binary::TYPE_GENERIC)];
        hash_update($this->ctx, $data);
        $this->bucket->chunkInsert($toUpload);
        $this->length += strlen($data);
        $this->n++;
    }

    private function fileCollectionInsert()
    {
        $md5 = hash_final($this->ctx);
        $this->file = array_merge($this->file, ['length' => $this->length, 'md5' => $md5]);
        $this->bucket->fileInsert($this->file);
        return $this->file['_id'];
    }
}
