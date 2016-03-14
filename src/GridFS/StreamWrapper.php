<?php

namespace MongoDB\GridFS;

/**
 * Stream wrapper for reading and writing a GridFS file.
 *
 * @internal
 * @see Bucket::openUploadStream()
 * @see Bucket::openDownloadStream()
 */
class StreamWrapper
{
    public $context;

    private $collectionsWrapper;
    private $gridFSStream;
    private $id;
    private $mode;

    public function getId()
    {
        return $this->id;
    }

    public function openReadStream()
    {
        $context = stream_context_get_options($this->context);
        $this->gridFSStream = new GridFSDownload($this->collectionsWrapper, $context['gridfs']['file']);
        $this->id = $this->gridFSStream->getId();

        return true;
    }

    public function openWriteStream()
    {
        $context = stream_context_get_options($this->context);
        $options = $context['gridfs']['uploadOptions'];
        $this->gridFSStream = new GridFSUpload($this->collectionsWrapper, $this->identifier, $options);
        $this->id = $this->gridFSStream->getId();

        return true;
    }

    /**
     * Register the GridFS stream wrapper.
     */
    public static function register()
    {
        if (in_array('gridfs', stream_get_wrappers())) {
            stream_wrapper_unregister('gridfs');
        }

        stream_wrapper_register('gridfs', get_called_class(), \STREAM_IS_URL);
    }

    public function stream_close()
    {
        $this->gridFSStream->close();
    }

    public function stream_eof()
    {
        return $this->gridFSStream->isEOF();
    }

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $this->initProtocol($path);
        $context = stream_context_get_options($this->context);
        $this->collectionsWrapper = $context['gridfs']['collectionsWrapper'];
        $this->mode = $mode;

        switch ($this->mode) {
            case 'r': return $this->openReadStream();
            case 'w': return $this->openWriteStream();
            default:  return false;
        }
    }

    public function stream_read($count)
    {
        return $this->gridFSStream->downloadNumBytes($count);
    }

    public function stream_stat()
    {
        $stat = $this->getStatTemplate();
        $stat[7] = $stat['size'] = $this->gridFSStream->getSize();

        return $stat;
    }

    public function stream_write($data)
    {
        $this->gridFSStream->insertChunks($data);

        return strlen($data);
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
        $this->identifier = substr($parsed_path['path'], 1);
    }
}
