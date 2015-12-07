<?php
namespace MongoDB\GridFS;

use MongoDB\Driver\Server;
use MongoDB\Collection;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedValueException;

/**
 * Stream wrapper for reading and writing a GridFS file.
 *
 * @internal
 * @see MongoDB\GridFS\Bucket::openUploadStream()
 */
class StreamWrapper
{
    public $context;
    private $filename;
    private $protocol = 'gridfs';
    private $mode;
    private $gridFsStream;
    private $bucket;

    /**
     * Register the GridFS stream wrapper.
     */
    public static function register()
    {
        if (in_array('gridfs', stream_get_wrappers())) {
            stream_wrapper_unregister('gridfs');
        }
        stream_wrapper_register('gridfs', get_called_class(), STREAM_IS_URL);
    }

    private function initProtocol($path)
    {
        $parsed_path = parse_url($path);
        $this->databaseName = $parsed_path["host"];
        $this->identifier = substr($parsed_path["path"], 1);
    }

    public function stream_write($data)
    {
        $this->gridFsStream->insertChunks($data);
        return strlen($data);
    }

    public function stream_read($count) {
        $out ="";
        if ($this->dirtyCache) {
            $out = fread($this->buffer, $count);
            if (strlen($out) == $count) {
                return $out;
            } else {
                fclose($out);
                $this->dirtyCache = false;
            }
              $this->n++;
        }

        if ($this->file->length <= $this->n) {
            return false;
        }

        while(strlen($out) < $count && $this ->n <$this->file->length) {
            $bytes_left = $count - strlen($out);
            $next = $this->chunksCollection->findOne(['files_id' => $this->file->_id, "n" => $this->n]);
            $out .= substr($next->data, 0, $bytes_left);
            $this->n++;
        }
        if ($bytes_left < strlen($next->data)) {
            $this->buffer = tmpfile();
            fwrite($this->buffer, substr($next->data, $bytes_left));
            $this->dirtyCache =true;
        }
        return $out;
    }

    public function stream_eof() {
        return $this->n >= $this->file->length;
    }

    public function stream_close() {
        $this->gridFsStream->close();

    }

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $this->initProtocol($path);
        $context = stream_context_get_options($this->context);
        $this->bucket =$context['gridfs']['bucket'];
        $this->mode = $mode;
        switch ($this->mode) {
            case 'w' : return $this ->openWriteStream();
            case 'r' : return $this ->openReadStream();
            default: return false;
        }
    }

    public function openWriteStream() {
        $context = stream_context_get_options($this->context);
        $options =$context['gridfs']['uploadOptions'];
        $this->gridFsStream = new GridFsUpload($this->bucket, $this->identifier, $options);
        return true;
    }

    public function openReadStream() {
        $objectId = new \MongoDB\BSON\ObjectId($this->identifier);
        $this->file = $this->filesCollection->findOne(['_id' => $objectId]);
        return true;
    }
}
