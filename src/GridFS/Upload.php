<?php

namespace MongoDB\GridFS;

use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedValueException;

/**
 * Writable stream for uploading a file to GridFS.
 *
 * @internal
 * @see MongoDB\GridFS\Bucket::openUploadStream()
 */
class Upload
{
    private $bucket;
    private $filename;
    private $options;

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

    function write($data)
    {
        hash_update($this->hashContext, $data);

        fopen('php://memory', )
    }
}
