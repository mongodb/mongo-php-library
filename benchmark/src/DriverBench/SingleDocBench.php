<?php

namespace MongoDB\Benchmark\DriverBench;

use Generator;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use MongoDB\BSON\Document;
use MongoDB\Driver\Command;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;

use function array_map;
use function file_get_contents;
use function range;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#single-doc-benchmarks
 */
#[BeforeMethods('prepareDatabase')]
final class SingleDocBench
{
    public function prepareDatabase(): void
    {
        Utils::getCollection()->drop();
    }

    /** @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#run-command */
    public function benchRunCommand(): void
    {
        $manager = Utils::getClient()->getManager();
        $database = Utils::getDatabase();

        for ($i = 0; $i < 10_000; $i++) {
            $manager->executeCommand($database, new Command(['hello' => true]));
        }
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#find-one-by-id
     * @param array{options: array} $params
     */
    #[BeforeMethods('beforeFindOneById')]
    #[ParamProviders('provideFindOneByIdParams')]
    public function benchFindOneById(array $params): void
    {
        $collection = Utils::getCollection();

        for ($id = 1; $id <= 10_000; $id++) {
            $collection->findOne(['_id' => $id], $params['options']);
        }
    }

    public function beforeFindOneById(): void
    {
        $tweet = Data::readJsonFile(Data::TWEET_FILE_PATH);
        $docs = array_map(fn ($id) => $tweet + ['_id' => $id], range(1, 10_000));
        Utils::getCollection()->insertMany($docs);
    }

    public static function provideFindOneByIdParams(): Generator
    {
        yield 'Driver default typemap' => [
            'options' => [],
        ];

        yield 'Raw BSON' => [
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#small-doc-insertone
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#large-doc-bulk-insert
     * @param array{document: object|array, repeat: int, options?: array} $params
     */
    #[ParamProviders('provideInsertOneParams')]
    public function benchInsertOne(array $params): void
    {
        $collection = Utils::getCollection();

        for ($i = $params['repeat']; $i > 0; $i--) {
            $collection->insertOne($params['document']);
        }
    }

    public static function provideInsertOneParams(): Generator
    {
        yield 'Small doc' => [
            'document' => Data::readJsonFile(Data::SMALL_FILE_PATH),
            'repeat' => 10_000,
        ];

        yield 'Small BSON doc' => [
            'document' => Document::fromJSON(file_get_contents(Data::SMALL_FILE_PATH)),
            'repeat' => 10_000,
        ];

        yield 'Large doc' => [
            'document' => Data::readJsonFile(Data::LARGE_FILE_PATH),
            'repeat' => 10,
        ];

        yield 'Large BSON doc' => [
            'document' => Document::fromJSON(file_get_contents(Data::LARGE_FILE_PATH)),
            'repeat' => 10,
        ];
    }
}
