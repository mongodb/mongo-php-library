<?php

namespace MongoDB\Benchmark\DriverBench;

use Amp\Future;
use Amp\Parallel\Worker\ContextWorkerFactory;
use Amp\Parallel\Worker\ContextWorkerPool;
use Generator;
use MongoDB\Benchmark\DriverBench\Amp\ImportFileTask;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use MongoDB\BSON\Document;
use MongoDB\Driver\BulkWrite;
use PhpBench\Attributes\AfterClassMethods;
use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Revs;
use RuntimeException;

use function array_map;
use function count;
use function fclose;
use function fgets;
use function file_get_contents;
use function file_put_contents;
use function fopen;
use function is_dir;
use function mkdir;
use function pcntl_fork;
use function pcntl_waitpid;
use function range;
use function sprintf;
use function str_repeat;
use function stream_get_line;
use function sys_get_temp_dir;
use function unlink;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#ldjson-multi-file-import
 */
#[BeforeClassMethods('beforeClass')]
#[AfterClassMethods('afterClass')]
#[BeforeMethods('beforeIteration')]
#[Iterations(1)]
#[Revs(1)]
final class ParallelMultiFileImportBench
{
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
    }

    public function beforeIteration(): void
    {
        $database = Utils::getDatabase();
        $database->drop();
        $database->createCollection(Utils::getCollectionName());
    }

    /**
     * Using Driver's BulkWrite in a single thread
     */
    public function benchMultiFileImportBulkWrite(): void
    {
        foreach (self::getFileNames() as $file) {
            self::importFile($file);
        }
    }

    /**
     * Using library's Collection::insertMany in a single thread
     */
    public function benchMultiFileImportInsertMany(): void
    {
        $collection = Utils::getCollection();
        foreach (self::getFileNames() as $file) {
            $docs = [];
            // Read file contents into BSON documents
            $fh = fopen($file, 'r');
            while (($line = fgets($fh)) !== false) {
                if ($line !== '') {
                    $docs[] = Document::fromJSON($line);
                }
            }

            fclose($fh);

            // Insert documents in bulk
            $collection->insertMany($docs);
        }
    }

    /**
     * Using multiple forked threads
     *
     * @param array{processes:int} $params
     */
    #[ParamProviders(['provideProcessesParameter'])]
    public function benchMultiFileImportFork(array $params): void
    {
        $pids = [];
        foreach (self::getFileNames() as $file) {
            // Wait for a child process to finish if we have reached the maximum number of processes
            if (count($pids) >= $params['processes']) {
                $pid = pcntl_waitpid(-1, $status);
                unset($pids[$pid]);
            }

            $pid = pcntl_fork();
            if ($pid === 0) {
                // Reset to ensure that the existing libmongoc client (via the Manager) is not re-used by the child
                // process. When the child process constructs a new Manager, the differing PID will result in creation
                // of a new libmongoc client.
                Utils::reset();
                self::importFile($file);

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

    /**
     * Using amphp/parallel with worker pool
     *
     * @param array{processes:int} $params
     */
    #[ParamProviders(['provideProcessesParameter'])]
    public function benchMultiFileImportAmp(array $params): void
    {
        $workerPool = new ContextWorkerPool($params['processes'], new ContextWorkerFactory());

        $futures = array_map(
            fn ($file) => $workerPool->submit(new ImportFileTask($file))->getFuture(),
            self::getFileNames(),
        );

        foreach (Future::iterate($futures) as $future) {
            $future->await();
        }

        $workerPool->shutdown();
    }

    public static function provideProcessesParameter(): Generator
    {
        yield '1 proc' => ['processes' => 1]; // 100 sequences, to compare to the single thread baseline
        yield '2 proc' => ['processes' => 2]; // 50 sequences
        yield '4 proc' => ['processes' => 4]; // 25 sequences
        yield '8 proc' => ['processes' => 8]; // 13 sequences
        yield '13 proc' => ['processes' => 13]; // 8 sequences
        yield '20 proc' => ['processes' => 20]; // 5 sequences
    }

    /**
     * We benchmarked the following solutions to read a file line by line:
     *  - file
     *  - SplFileObject
     *  - fgets
     *  - stream_get_line 🏆
     */
    public static function importFile(string $file): void
    {
        $namespace = sprintf('%s.%s', Utils::getDatabaseName(), Utils::getCollectionName());

        $bulkWrite = new BulkWrite();
        $fh = fopen($file, 'r');
        while (($line = stream_get_line($fh, 10_000, "\n")) !== false) {
            $bulkWrite->insert(Document::fromJSON($line));
        }

        fclose($fh);
        Utils::getClient()->getManager()->executeBulkWrite($namespace, $bulkWrite);
    }

    /**
     * Using a method to regenerate the file names because we cannot cache the result of the method in a static
     * property. The benchmark runner will call the method in a different process, so the static property will not be
     * populated.
     */
    private static function getFileNames(): array
    {
        $tempDir = sys_get_temp_dir() . '/mongodb-php-benchmark';
        if (! is_dir($tempDir)) {
            mkdir($tempDir);
        }

        return array_map(
            static fn (int $i) => sprintf('%s/%03d.txt', $tempDir, $i),
            range(0, 99),
        );
    }
}
