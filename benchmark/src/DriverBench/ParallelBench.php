<?php

namespace MongoDB\Benchmark\DriverBench;

use Amp\Parallel\Worker\DefaultPool;
use Generator;
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

use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;
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
use function sys_get_temp_dir;
use function unlink;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#parallel
 */
#[BeforeClassMethods('beforeClass')]
#[AfterClassMethods('afterClass')]
final class ParallelBench
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

    /**
     * Parallel: LDJSON multi-file import
     * Using Driver's BulkWrite in a single thread
     */
    #[BeforeMethods('beforeMultiFileImport')]
    #[Revs(1)]
    #[Iterations(1)]
    public function benchMultiFileImportBulkWrite(): void
    {
        foreach (self::getFileNames() as $file) {
            self::importFile($file);
        }
    }

    /**
     * Parallel: LDJSON multi-file import
     * Using library's Collection::insertMany in a single thread
     */
    #[BeforeMethods('beforeMultiFileImport')]
    #[Revs(1)]
    #[Iterations(1)]
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
     * Parallel: LDJSON multi-file import
     * Using multiple forked threads
     *
     * @param array{processes:int, files:string[], batchSize:int} $params
     */
    #[BeforeMethods('beforeMultiFileImport')]
    #[ParamProviders(['provideProcessesParameter'])]
    #[Revs(1)]
    #[Iterations(1)]
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
     * Parallel: LDJSON multi-file import
     * Using amphp/parallel-functions with worker pool
     *
     * @param array{processes:int, files:string[], batchSize:int} $params
     */
    #[BeforeMethods('beforeMultiFileImport')]
    #[ParamProviders(['provideProcessesParameter'])]
    #[Revs(1)]
    #[Iterations(1)]
    public function benchMultiFileImportAmp(array $params): void
    {
        wait(parallelMap(
            self::getFileNames(),
            // Uses array callable instead of closure to skip complex serialization
            [self::class, 'importFile'],
            // The pool size is the number of processes
            new DefaultPool($params['processes']),
        ));
    }

    public static function provideProcessesParameter(): Generator
    {
        yield '1 proc' => ['processes' => 1]; // 100 sequences, to compare to the single thread baseline
        yield '2 proc' => ['processes' => 2]; // 50 sequences
        yield '4 proc' => ['processes' => 4]; // 25 sequences
        yield '8 proc' => ['processes' => 8]; // 13 sequences
        yield '13 proc' => ['processes' => 13]; // 8 sequences
        yield '20 proc' => ['processes' => 20]; // 5 sequences
        yield '34 proc' => ['processes' => 34]; // 3 sequences
    }

    public function beforeMultiFileImport(): void
    {
        $database = Utils::getDatabase();
        $database->drop();
        $database->createCollection(Utils::getCollectionName());
    }

    public static function importFile(string $file): void
    {
        $namespace = sprintf('%s.%s', Utils::getDatabaseName(), Utils::getCollectionName());

        $bulkWrite = new BulkWrite();
        $fh = fopen($file, 'r');
        while (($line = fgets($fh)) !== false) {
            if ($line !== '') {
                $bulkWrite->insert(Document::fromJSON($line));
            }
        }

        fclose($fh);
        Utils::getClient()->getManager()->executeBulkWrite($namespace, $bulkWrite);
    }

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
