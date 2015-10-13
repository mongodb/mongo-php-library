<?php

namespace MongoDB\GridFS;

use MongoDB\Driver\Server;
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

    private $bucket;
    private $filename;
    private $options;

    private $protocol = 'gridfs';

    private $at;
    private $hashContext;

    /**
     * Constructs a writable upload stream.
     *
     * Supported options:
     *
     *  * chunkSizeBytes (integer): The number of bytes per chunk of this file.
     *    Defaults to the chunkSizeBytes of the Bucket.
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * The following options are deprecated:
     *
     *  * aliases (string[]): An array of aliases (i.e. filenames). Applications
     *    wishing to store aliases should add an aliases field to the metadata
     *    document instead.
     *
     *  * contentType (string): A valid MIME type. Applications wishing to store
     *    a contentType should add a contentType field to the metadata document
     *    instead.
     *
     * @param Bucket $bucket   Database name
     * @param string $filename Filename
     * @param array  $options  Upload options
     * @throws InvalidArgumentException
     */
    public function __construct(Bucket $bucket, $filename, array $options = [])
    {
        $options += [
            'chunkSizeBytes' => $bucket->getChunkSizeBytes(),
        ];

        if (isset($options['chunkSizeBytes']) && ! is_integer($options['chunkSizeBytes'])) {
            throw new InvalidArgumentTypeException('"chunkSizeBytes" option', $options['chunkSizeBytes'], 'integer');
        }

        if (isset($options['metadata']) && ! is_array($options['metadata']) && ! is_object($options['metadata'])) {
            throw new InvalidArgumentTypeException('"metadata" option', $options['metadata'], 'array or object');
        }

        if (isset($options['aliases'])) {
            if ( ! is_array($options['aliases'])) {
                throw new InvalidArgumentTypeException('"aliases" option', $options['aliases'], 'array or object');
            }

            $expectedIndex = 0;

            foreach ($options['aliases'] as $i => $alias) {
                if ($i !== $expectedIndex) {
                    throw new InvalidArgumentException(sprintf('"aliases" option is not a list (unexpected index: "%s")', $i));
                }

                if ( ! is_string($alias)) {
                    throw new InvalidArgumentTypeException(sprintf('$options["aliases"][%d]', $i), $alias, 'string');
                }

                $expectedIndex += 1;
            }
        }

        if (isset($options['contentType']) && ! is_string($options['contentType'])) {
            throw new InvalidArgumentTypeException('"contentType" option', $options['contentType'], 'string');
        }

        $this->bucket = $bucket;
        $this->filename = (string) $filename;
        $this->options = $options;
        $this->hashContext = hash_init('md5');
    }

    public function stream_write($data)
    {
        hash_update($this->hashContext, $data);

        //fopen('php://memory', )
    }

    /**
     * Register the GridFS stream wrapper.
     *
     * @param Manager $manager  Manager instance from the driver
     * @param string  $protocol Protocol to register
     */
    public static function register(Manager $manager, $protocol = 'gridfs')
    {
        if (in_array($protocol, stream_get_wrappers())) {
            stream_wrapper_unregister($protocol);
        }

        // Set the client passed in as the default stream context client
        stream_wrapper_register($protocol, get_called_class(), STREAM_IS_URL);
        $default = stream_context_get_options(stream_context_get_default());
        $default[$protocol]['manager'] = $manager;
        stream_context_set_default($default);
    }

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $this->initProtocol($path);
        $this->params = $this->getDatabase($path);
        $this->mode = rtrim($mode, 'bt');

        if ($errors = $this->validate($path, $this->mode)) {
            return $this->triggerError($errors);
        }

        return $this->boolCall(function() use ($path) {
            switch ($this->mode) {
                case 'r': return $this->openReadStream($path);
                case 'a': return $this->openAppendStream($path);
                default: return $this->openWriteStream($path);
            }
        });
    }

    private function validate($path, $mode)
    {
        $errors = [];

        if (!in_array($mode, ['r', 'w', 'a', 'x'])) {
            $errors[] = "Mode not supported: {$mode}. "
                . "Use one 'r', 'w', 'a', or 'x'.";
        }

        return $errors;
    }

    /**
     * Trigger one or more errors
     *
     * @param string|array $errors Errors to trigger
     * @param mixed        $flags  If set to STREAM_URL_STAT_QUIET, then no
     *                             error or exception occurs
     *
     * @return bool Returns false
     * @throws \RuntimeException if throw_errors is true
     */
    private function triggerError($errors, $flags = null)
    {
        // This is triggered with things like file_exists()
        if ($flags & STREAM_URL_STAT_QUIET) {
            return $flags & STREAM_URL_STAT_LINK
                // This is triggered for things like is_link()
                ? $this->formatUrlStat(false)
                : false;
        }
        // This is triggered when doing things like lstat() or stat()
        trigger_error(implode("\n", (array) $errors), E_USER_WARNING);
        return false;
    }
}
