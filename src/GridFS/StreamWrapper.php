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
    private $collectionsWrapper;

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
    public function stream_write($data)
    {
        $this->gridFsStream->insertChunks($data);
        return strlen($data);
    }
    public function stream_read($count)
    {
        return $this->gridFsStream->downloadNumBytes($count);
    }
    public function stream_eof()
    {
        return $this->gridFsStream->isEOF();
    }
    public function stream_close()
    {
        $this->gridFsStream->close();
    }
    public function stream_stat()
    {
        $stat = $this->getStatTemplate();
        $stat[7] = $stat['size'] = $this->gridFsStream->getSize();
        $stat[2] = $stat['mode'] = $this->mode;
        return $stat;
    }
    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $this->initProtocol($path);
        $context = stream_context_get_options($this->context);
        $this->collectionsWrapper =$context['gridfs']['collectionsWrapper'];
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
        $this->gridFsStream = new GridFsUpload($this->collectionsWrapper, $this->identifier, $options);
        return true;
    }

    public function openReadStream() {
        $context = stream_context_get_options($this->context);
        $this->gridFsStream = new GridFsDownload($this->collectionsWrapper, $context['gridfs']['file']);
        return true;
    }

    /**
    * Gets a URL stat template with default values
    * from https://github.com/aws/aws-sdk-php/blob/master/src/S3/StreamWrapper.php
    * @return array
    */
    private function getStatTemplate()
    {
        return [
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
        ];
    }
    private function initProtocol($path)
    {
        $parsed_path = parse_url($path);
        $this->databaseName = $parsed_path["host"];
        $this->identifier = substr($parsed_path["path"], 1);
    }
}
