<?php

namespace MongoDB\Benchmark\DriverBench;

use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use PhpBench\Attributes\BeforeMethods;

use function array_map;
use function basename;
use function fopen;
use function is_dir;
use function mkdir;
use function pcntl_fork;
use function pcntl_waitpid;
use function range;
use function sprintf;
use function stream_copy_to_stream;
use function sys_get_temp_dir;
use function unlink;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#parallel
 */
#[AfterMethods('cleanup')]
final class ParallelGridFSBench
{
    public static function cleanup(): void
    {
        Utils::getDatabase()->drop();

        foreach (self::getFileNames() as $file) {
            unlink($file);
        }
    }

    /**
     * GridFS multi-file upload
     *
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#gridfs-multi-file-upload
     */
    #[BeforeMethods('beforeUpload')]
    public function benchUpload(): void
    {
        $pids = [];
        foreach (self::getFileNames() as $file) {
            $pid = pcntl_fork();
            if ($pid === 0) {
                Utils::getDatabase()->selectGridFSBucket()->uploadFromStream(basename($file), fopen($file, 'r'));

                // Exit the child process
                exit(0);
            }

            if ($pid === -1) {
                throw new RuntimeException('Failed to fork');
            }

            // Keep the forked process id to wait for it later
            $pids[$pid] = true;
        }

        // Wait for all child processes to finish
        while ($pids !== []) {
            $pid = pcntl_waitpid(-1, $status);
            unset($pids[$pid]);
        }
    }

    public function beforeUpload(): void
    {
        foreach (self::getFileNames() as $file) {
            stream_copy_to_stream(Data::getStream(5 * 1024 * 1024), fopen($file, 'w'));
        }

        $database = Utils::getDatabase();
        $database->drop();

        $bucket = $database->selectGridFSBucket();
        $bucket->uploadFromStream('init', Data::getStream(1));

        Utils::reset();
    }

    /**
     * GridFS multi-file download
     *
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#gridfs-multi-file-download
     */
    #[BeforeMethods('beforeDownload')]
    public function benchDownload(): void
    {
        $pids = [];
        foreach (self::getFileNames() as $file) {
            $pid = pcntl_fork();
            if ($pid === 0) {
                $stream = Utils::getDatabase()
                    ->selectGridFSBucket()
                    ->openDownloadStreamByName(basename($file));
                stream_copy_to_stream($stream, fopen($file, 'w'));

                // Exit the child process
                exit(0);
            }

            if ($pid === -1) {
                throw new RuntimeException('Failed to fork');
            }

            // Keep the forked process id to wait for it later
            $pids[$pid] = true;
        }

        // Wait for all child processes to finish
        while ($pids !== []) {
            $pid = pcntl_waitpid(-1, $status);
            unset($pids[$pid]);
        }
    }

    public function beforeDownload(): void
    {
        // Initialize the GridFS bucket with the files
        $this->beforeUpload();
        $this->benchUpload();
    }

    private static function getFileNames(): array
    {
        $tempDir = sys_get_temp_dir() . '/mongodb-php-benchmark';
        if (! is_dir($tempDir)) {
            mkdir($tempDir);
        }

        return array_map(
            static fn (int $i) => sprintf('%s/file%02d.txt', $tempDir, $i),
            range(0, 49),
        );
    }
}
