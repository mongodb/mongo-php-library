<?php

namespace MongoDB\Benchmark\DriverBench;

use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use MongoDB\GridFS\Bucket;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\BeforeMethods;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#multi-doc-benchmarks
 */
#[AfterMethods('afterAll')]
final class GridFSBench
{
    /** @var resource */
    private $stream;
    private Bucket $bucket;
    private mixed $id;

    /** @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#gridfs-upload */
    #[BeforeMethods('beforeUpload')]
    public function benchUpload(): void
    {
        $this->bucket->uploadFromStream('test', $this->stream);
    }

    public function beforeUpload(): void
    {
        $database = Utils::getDatabase();
        $database->drop();

        $this->bucket = $database->selectGridFSBucket();
        // Init the GridFS bucket
        $this->bucket->uploadFromStream('init', Data::getStream(1));
        // Prepare the 50MB stream to upload
        $this->stream = Data::getStream(50 * 1024 * 1024);
    }

    /** @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#gridfs-download */
    #[BeforeMethods('beforeDownload')]
    public function benchDownload(): void
    {
        $this->bucket->downloadToStream($this->id, $this->stream);
    }

    public function beforeDownload(): void
    {
        $database = Utils::getDatabase();
        $database->drop();

        $this->bucket = $database->selectGridFSBucket();
        // Upload a 50MB file
        $this->id = $this->bucket->uploadFromStream('init', Data::getStream(50 * 1024 * 1024));
        // Prepare the stream to receive the download
        $this->stream = Data::getStream(0);
    }

    public function afterAll(): void
    {
        unset($this->bucket, $this->stream, $this->id);
        Utils::getDatabase()->drop();
    }
}
