<?php

namespace MongoDB\Benchmark\DriverBench;

use Amp\Future;
use Amp\Parallel\Worker\ContextWorkerFactory;
use Amp\Parallel\Worker\ContextWorkerPool;
use Generator;
use MongoDB\Benchmark\DriverBench\Amp\ExportFileTask;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use MongoDB\BSON\Document;
use PhpBench\Attributes\AfterClassMethods;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use RuntimeException;

use function array_chunk;
use function array_fill;
use function array_map;
use function ceil;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function json_encode;
use function mkdir;
use function pcntl_fork;
use function pcntl_waitpid;
use function range;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#ldjson-multi-file-export
 */
#[BeforeClassMethods('beforeClass')]
#[AfterClassMethods('afterClass')]
#[AfterMethods('afterIteration')]
#[Iterations(1)]
final class ParallelMultiFileExportBench
{
    public static function beforeClass(): void
    {
        // Resets the database to ensure that the collection is empty
        Utils::getDatabase()->drop();

        $doc = Document::fromJSON(file_get_contents(Data::LDJSON_FILE_PATH));
        Utils::getCollection()->insertMany(array_fill(0, 500_000, $doc));
    }

    public static function afterClass(): void
    {
        Utils::getDatabase()->drop();
    }

    public function afterIteration(): void
    {
        foreach (self::getFileNames() as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Using a single thread to export multiple files.
     * By executing a single Find command for multiple files, we can reduce the number of roundtrips to the server.
     *
     * @param array{chunkSize:int} $params
     */
    #[ParamProviders(['provideChunkParams'])]
    public function benchSequential(array $params): void
    {
        foreach (array_chunk(self::getFileNames(), $params['chunkSize']) as $i => $files) {
            self::exportFile($files, [], [
                'limit' => 5_000 * $params['chunkSize'],
                'skip' => 5_000 * $params['chunkSize'] * $i,
            ]);
        }
    }

    /**
     * Using multiple forked threads
     *
     * @param array{chunk:int} $params
     */
    #[ParamProviders(['provideChunkParams'])]
    public function benchFork(array $params): void
    {
        $pids = [];

        // Reset to ensure that the existing libmongoc client (via the Manager) is not re-used by the child
        // process. When the child process constructs a new Manager, the differing PID will result in creation
        // of a new libmongoc client.
        Utils::reset();

        // Create a child process for each chunk of files
        foreach (array_chunk(self::getFileNames(), $params['chunkSize']) as $i => $files) {
            $pid = pcntl_fork();
            if ($pid === 0) {
                self::exportFile($files, [], [
                    'limit' => 5_000 * $params['chunkSize'],
                    'skip' => 5_000 * $params['chunkSize'] * $i,
                ]);

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
     * @param array{chunkSize:int} $params
     */
    #[ParamProviders(['provideChunkParams'])]
    public function benchAmpWorkers(array $params): void
    {
        $workerPool = new ContextWorkerPool(ceil(100 / $params['chunkSize']), new ContextWorkerFactory());

        $futures = [];
        foreach (array_chunk(self::getFileNames(), $params['chunkSize']) as $i => $files) {
            $futures[] = $workerPool->submit(
                new ExportFileTask(
                    files: $files,
                    options: [
                        'limit' => 5_000 * $params['chunkSize'],
                        'skip' => 5_000 * $params['chunkSize'] * $i,
                    ],
                ),
            )->getFuture();
        }

        foreach (Future::iterate($futures) as $future) {
            $future->await();
        }
    }

    public static function provideChunkParams(): Generator
    {
        yield '100 chunks' => ['chunkSize' => 1];
        yield '25 chunks' => ['chunkSize' => 4];
        yield '10 chunks' => ['chunkSize' => 10];
    }

    /**
     * Export a query to a file
     */
    public static function exportFile(array|string $files, array $filter = [], array $options = []): void
    {
        $options += [
            // bson typemap is faster on query result, but slower to JSON encode
            'typeMap' => ['root' => 'array'],
            // Excludes _id field to be identical to fixtures data
            'projection' => ['_id' => 0],
            'sort' => ['_id' => 1],
        ];
        $cursor = Utils::getCollection()->find($filter, $options);
        $cursor->rewind();

        foreach ((array) $files as $file) {
            // Aggregate file in memory to reduce filesystem operations
            $data = '';
            for ($i = 0; $i < 5_000; $i++) {
                $document = $cursor->current();
                // Cursor exhausted
                if (! $document) {
                    break;
                }

                // We don't use MongoDB\BSON\Document::toCanonicalExtendedJSON() because
                // it is slower than json_encode() on an array.
                $data .= json_encode($document) . "\n";
                $cursor->next();
            }

            // Write file in a single operation
            file_put_contents($file, $data);
        }
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
