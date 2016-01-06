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
 */
class GridFsUpload
{
    private $ctx;
    private $bufferLength = 0;
    private $indexChecker;
    private $length = 0;
    private $collectionsWrapper;
    private $chunkOffset = 0;
    private $chunkSize;
    private $buffer;
    private $file;
    private $isClosed = false;
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
     *  * chunkSizeBytes: size of each chunk
     *
     * @param GridFSCollectionsWrapper $collectionsWrapper  Files Collection
     * @param string                   $filename            Filename to insert
     * @param array                    $options             File options
     * @throws InvalidArgumentException
     */
    public function __construct(
        GridFsCollectionsWrapper $collectionsWrapper,
        $filename,
        array $options=[]
        )
    {
        $this->ctx = hash_init('md5');
        $this->collectionsWrapper = $collectionsWrapper;
        $this->buffer = fopen('php://temp', 'w+');

        $options +=['chunkSizeBytes' => 261120];
        $this->chunkSize = $options['chunkSizeBytes'];

        $time = $this->millitime();
        $uploadDate = new \MongoDB\BSON\UTCDateTime($time);
        $objectId = new \MongoDB\BSON\ObjectId();
        $main_file = [
            "chunkSize" => $this->chunkSize,
            "filename" => $filename,
            "uploadDate" => $uploadDate,
            "_id" => $objectId
        ];

        $fileOptions = [];
        if (isset($options['contentType'])) {
            if (is_string($options['contentType'])) {
                $fileOptions['contentType'] = $options['contentType'];
            } else {
                throw new \MongoDB\Exception\InvalidArgumentTypeException('"contentType" option', $options['contentType'], 'string');
            }
        }
        if (isset($options['aliases'])) {
            if (\MongoDB\is_string_array($options['aliases'])) {
                $fileOptions['aliases'] = $options['aliases'];
            } else {
                throw new \MongoDB\Exception\InvalidArgumentTypeException('"aliases" option', $options['aliases'], 'array of strings');
            }
        }

        if (isset($options['metadata'])) {
            if (is_array($options['metadata']) || is_object($options['metadata'])) {
                $fileOptions['metadata'] = $options['metadata'];
            } else {
                throw new \MongoDB\Exception\InvalidArgumentTypeException('"metadata" option', $options['metadata'], 'object or array');
            }
        }
        $this->file = array_merge($main_file, $fileOptions);

    }
    /**
    * Reads data from a stream into GridFS
    *
    * @param Stream   $source   Source Stream
    * @return ObjectId
    */
    public function uploadFromStream($source)
    {
        if (!is_resource($source) || get_resource_type($source) != "stream") {
            throw new UnexpectedTypeException('stream', $source);
        } else{
            $streamMetadata = stream_get_meta_data($source);
        } if (!is_readable($streamMetadata['uri'])) {
     //       throw new InvalidArgumentException("stream not readable");
            //issue being that php's is_readable reports native streams as not readable like php://temp
        }
        while ($data = fread($source, $this->chunkSize)) {
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
        if($this->isClosed){
            return;
        }
        $readBytes = 0;
        while($readBytes != strlen($toWrite)) {
            $addToBuffer = substr($toWrite, $readBytes, $this->chunkSize - $this->bufferLength);
            fwrite($this->buffer, $addToBuffer);
            $readBytes += strlen($addToBuffer);
            $this->bufferLength += strlen($addToBuffer);
            if($this->bufferLength == $this->chunkSize) {
                rewind($this->buffer);
                $this->insertChunk(stream_get_contents($this->buffer));
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
        if($this->isClosed){
            return;
        }
        rewind($this->buffer);
        $cached = stream_get_contents($this->buffer);

        if(strlen($cached) > 0) {
            $this->insertChunk($cached);
        }
        fclose($this->buffer);
        $this->fileCollectionInsert();
        $this->isClosed = true;
    }
    public function getSize()
    {
        return $this->length;
    }
    public function getId()
    {
        return $this->file["_id"];
    }
    public function getLength()
    {
        return $this->length;
    }
    public function getChunkSize()
    {
        return $this->chunkSize;
    }
    public function getFile()
    {
        return $this->file;
    }
    public function isEOF()
    {
        return $this->isClosed;
    }
    private function abort()
    {
        $this->collectionsWrapper->getChunksCollection()->deleteMany(["files_id"=> $this->file["_id"]]);
        $this->collectionsWrapper->getFilesCollection()->deleteOne(["_id"=> $this->file['_id']]);
        $this->isClosed = true;
    }
    private function insertChunk($data)
    {
        if($this->isClosed){
            return;
        }
        $toUpload = ["files_id" => $this->file['_id'], "n" => $this->chunkOffset, "data" => new \MongoDB\BSON\Binary($data, \MongoDB\BSON\Binary::TYPE_GENERIC)];
        hash_update($this->ctx, $data);
        try{
            $this->collectionsWrapper->chunkInsert($toUpload);
        } catch (\MongoDB\Exception $e){
            $this->abort();
            throw $e;
        }
        $this->length += strlen($data);
        $this->chunkOffset++;
    }
    private function fileCollectionInsert()
    {
        if($this->isClosed){
            return;
        }
        $md5 = hash_final($this->ctx);
        $this->file = array_merge($this->file, ['length' => $this->length, 'md5' => $md5]);
        try{
            $this->collectionsWrapper->fileInsert($this->file);
        } catch (\MongoDB\Exception $e){
            $this->abort();
            throw $e;
        }
        return $this->file['_id'];
    }
    //from: http://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
    private function millitime() {
      $microtime = microtime();
      $comps = explode(' ', $microtime);
      return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
}
