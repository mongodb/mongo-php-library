<?php

namespace MongoDB\GridFS;

use MongoDB\Collection;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentTypeException;

/**
 * GridFSCollectionsWrapper abstracts the GridFS files and chunks collections.
 * 
 * @internal
 */
class GridFSCollectionsWrapper
{
    private $chunksCollection;
    private $ensuredIndexes = false;
    private $filesCollection;

    /**
     * Constructs a GridFS collection wrapper.
     *
     * @see Collection::__construct() for supported options
     * @param Manager $manager           Manager instance from the driver
     * @param string  $databaseName      Database name
     * @param string  $bucketName        Bucket name
     * @param array   $collectionOptions Collection options
     * @throws InvalidArgumentException
     */
    public function __construct(Manager $manager, $databaseName, $bucketName, array $collectionOptions = [])
    {
        $this->filesCollection = new Collection($manager, sprintf('%s.%s.files', $databaseName, $bucketName), $collectionOptions);
        $this->chunksCollection = new Collection($manager, sprintf('%s.%s.chunks', $databaseName, $bucketName), $collectionOptions);
    }

    public function getChunksCollection()
    {
        return $this->chunksCollection;
    }

    public function getFilesCollection()
    {
        return $this->filesCollection;
    }

    public function insertChunk($chunk)
    {
        $this->ensureIndexes();
        $this->chunksCollection->insertOne($chunk);
    }

    public function insertFile($file)
    {
        $this->ensureIndexes();
        $this->filesCollection->insertOne($file);
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

    private function isFilesCollectionEmpty()
    {
        return null === $this->filesCollection->findOne([], [
            'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
            'projection' => ['_id' => 1],
        ]);
    }
}
