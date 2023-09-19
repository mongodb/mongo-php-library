<?php

namespace MongoDB\Benchmark\DriverBench;

use Generator;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use MongoDB\BSON\Document;
use MongoDB\Collection;
use PhpBench\Attributes\AfterClassMethods;
use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Revs;
use RuntimeException;

use function array_chunk;
use function array_map;
use function ceil;
use function count;
use function file;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function pcntl_fork;
use function pcntl_waitpid;
use function range;
use function sprintf;
use function str_repeat;
use function sys_get_temp_dir;
use function unlink;

use const FILE_IGNORE_NEW_LINES;
use const FILE_NO_DEFAULT_CONTEXT;
use const FILE_SKIP_EMPTY_LINES;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#parallel
 */
#[BeforeClassMethods('beforeClass')]
#[AfterClassMethods('afterClass')]
final class ParallelBench
{
    /** @var string[] */
    private static array $files = [];

    public static function beforeClass(): void
    {
        // Generate files
        $fileContents = str_repeat(file_get_contents(Data::LDJSON_FILE_PATH), 5_000);
        foreach (self::getFileNames() as $file) {
            file_put_contents($file, $fileContents);
        }
    }

    public static function afterClass(): void
    {
        foreach (self::getFileNames() as $file) {
            unlink($file);
        }

        self::$files = [];
    }

    /**
     * Parallel: LDJSON multi-file import
     * Using single thread
     *
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#ldjson-multi-file-import
     */
    #[BeforeMethods('beforeMultiFileImport')]
    #[Revs(1)]
    public function benchMultiFileImport(): void
    {
        $collection = Utils::getCollection();
        foreach (self::getFileNames() as $file) {
            self::importFile($file, $collection);
        }
    }

    /**
     * Parallel: LDJSON multi-file import
     * Using multiple forked threads
     *
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#ldjson-multi-file-import
     * @param array{processes:int, files:string[], batchSize:int} $params
     */
    #[BeforeMethods('beforeMultiFileImport')]
    #[ParamProviders(['provideProcessesParameter', 'provideMultiFileImportParameters'])]
    #[Revs(1)]
    public function benchMultiFileImportFork(array $params): void
    {
        $pids = [];
        foreach ($params['files'] as $files) {
            // Wait for a child process to finish if we have reached the maximum number of processes
            if (count($pids) >= $params['processes']) {
                $pid = pcntl_waitpid(-1, $status);
                unset($pids[$pid]);
            }

            $pid = pcntl_fork();
            if ($pid === 0) {
                // If we reset, we can garantee that we get a new manager in the child process
                // If we don't reset, we will get the same manager client_zval in the child process
                // and share the libmongoc client.
                Utils::reset();
                $collection = Utils::getCollection();

                foreach ($files as $file) {
                    self::importFile($file, $collection);
                }

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

    public static function provideProcessesParameter(): Generator
    {
        // Max number of forked processes
        for ($i = 1; $i <= 30; $i = (int) ceil($i * 1.25)) {
            yield $i . 'fork' => ['processes' => $i];
        }
    }

    public static function provideMultiFileImportParameters(): Generator
    {
        $files = self::getFileNames();

        // Chunk of file names to be handled by each processes
        for ($i = 1; $i <= 10; $i += 3) {
            yield 'by ' . $i => ['files' => array_chunk($files, $i)];
        }
    }

    public function beforeMultiFileImport(): void
    {
        $database = Utils::getDatabase();
        $database->drop();
        $database->createCollection(Utils::getCollectionName());
    }

    public function afterMultiFileImport(): void
    {
        foreach (self::$files as $file) {
            unlink($file);
        }

        unset($this->files);
    }

    private static function importFile(string $file, Collection $collection): void
    {
        // Read file contents into BSON documents
        $docs = array_map(
            static fn (string $line) => Document::fromJSON($line),
            file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES | FILE_NO_DEFAULT_CONTEXT),
        );
        // Insert documents in bulk
        $collection->insertMany($docs);
    }

    private static function getFileNames(): array
    {
        $tempDir = sys_get_temp_dir() . '/mongodb-php-benchmark';
        if (! is_dir($tempDir)) {
            mkdir($tempDir);
        }

        return array_map(
            static fn (int $i) => sprintf('%s/%03d.txt', $tempDir, $i),
            //range(0, 99),
            range(0, 5),
        );
    }
}
