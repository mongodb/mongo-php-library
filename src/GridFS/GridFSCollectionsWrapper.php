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
 */

//rename to context options
class GridFSCollectionsWrapper
{
    private $filesCollection;
    private $chunksCollection;
    private $ensuredIndexes = false;
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
    public function __construct(Manager $manager, $databaseName, $options)
    {
        $collectionOptions = [];

        if (isset($options['bucketName']) && ! is_string($options['bucketName'])) {
            throw new InvalidArgumentTypeException('"bucketName" option', $options['bucketName'], 'string');
        }
        if (isset($options['chunkSizeBytes']) && ! is_integer($options['chunkSizeBytes'])) {
            throw new InvalidArgumentTypeException('"chunkSizeBytes" option', $options['chunkSizeBytes'], 'integer');
        }
        if (isset($options['readPreference'])) {
            if (! $options['readPreference'] instanceof ReadPreference) {
                throw new InvalidArgumentTypeException('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
            } else {
                $collectionOptions['readPreference'] = $options['readPreference'];
            }
        }
        if (isset($options['writeConcern'])) {
            if (! $options['writeConcern'] instanceof WriteConcern) {
                throw new InvalidArgumentTypeException('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
            } else {
                $collectionOptions['writeConcern'] = $options['writeConcern'];
            }
        }
        $this->filesCollection = new Collection(
            $manager,
            sprintf('%s.%s.files', $databaseName, $options['bucketName']),
            $collectionOptions
        );
        $this->chunksCollection = new Collection(
            $manager,
            sprintf('%s.%s.chunks', $databaseName, $options['bucketName']),
            $collectionOptions
        );
    }

    public function chunkInsert($toUpload)
    {
        $this->ensureIndexes();
        $this->chunksCollection->insertOne($toUpload);
    }
    public function fileInsert($toUpload)
    {
        $this->ensureIndexes();
        $this->filesCollection->insertOne($toUpload);
    }
    public function getChunksCollection()
    {
        return $this->chunksCollection;
    }
    public function getFilesCollection()
    {
        return $this->filesCollection;
    }


    private function ensureIndexes()
    {
        if ($this->ensuredIndexes) {
            return;
        }
        if ( ! $this->isFilesCollectionEmpty()) {
            return;
        }
        $this->ensureFilesIndex();
        $this->ensureChunksIndex();
        $this->ensuredIndexes = true;
    }
    private function ensureChunksIndex()
    {
        foreach ($this->chunksCollection->listIndexes() as $index) {
            if ($index->isUnique() && $index->getKey() === ['files_id' => 1, 'n' => 1]) {
                return;
            }
        }
        $this->chunksCollection->createIndex(['files_id' => 1, 'n' => 1], ['unique' => true]);
    }
    private function ensureFilesIndex()
    {
        foreach ($this->filesCollection->listIndexes() as $index) {
            if ($index->getKey() === ['filename' => 1, 'uploadDate' => 1]) {
                return;
            }
        }
        $this->filesCollection->createIndex(['filename' => 1, 'uploadDate' => 1]);
    }
    private function isFilesCollectionEmpty()
    {
        return null === $this->filesCollection->findOne([], [
            'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
            'projection' => ['_id' => 1],
        ]);
    }
}
