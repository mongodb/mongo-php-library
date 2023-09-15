<?php

namespace MongoDB\Benchmark\DriverBench;

use Generator;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Utils;
use MongoDB\BSON\Document;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\ParamProviders;

use function array_fill;
use function file_get_contents;

/**
 * For accurate results, run benchmarks on a standalone server.
 *
 * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#multi-doc-benchmarks
 */
final class MultiDocBench
{
    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/benchmarking/benchmarking.rst#find-many-and-empty-the-cursor
     * @param array{options: array} $params
     */
    #[BeforeMethods('beforeFindMany')]
    #[ParamProviders('provideFindManyParams')]
    public function benchFindMany(array $params): void
    {
        $collection = Utils::getCollection();

        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        // phpcs:ignore Generic.ControlStructures.InlineControlStructure.NotAllowed
        foreach ($collection->find([], $params['options']) as $document);
    }

    public function beforeFindMany(): void
    {
        $collection = Utils::getCollection();
        $collection->drop();

        $tweet = Data::readJsonFile(Data::TWEET_FILE_PATH);
        $documents = array_fill(0, 9_999, $tweet);
        $collection->insertMany($documents);
    }

    public static function provideFindManyParams(): Generator
    {
        yield 'Driver default typemap' => [
            'options' => [],
        ];

        yield 'Raw BSON' => [
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#small-doc-bulk-insert
     * @see https://github.com/mongodb/specifications/blob/ddfc8b583d49aaf8c4c19fa01255afb66b36b92e/source/benchmarking/benchmarking.rst#large-doc-bulk-insert
     * @param array{documents: array} $params
     */
    #[BeforeMethods('beforeBulkInsert')]
    #[ParamProviders('provideBulkInsertParams')]
    public function benchBulkInsert(array $params): void
    {
        $collection = Utils::getCollection();

        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        // phpcs:ignore Generic.ControlStructures.InlineControlStructure.NotAllowed
        $collection->insertMany($params['documents']);
    }

    public function beforeBulkInsert(): void
    {
        $database = Utils::getDatabase();
        $database->dropCollection(Utils::getCollectionName());
        $database->createCollection(Utils::getCollectionName());
    }

    public static function provideBulkInsertParams(): Generator
    {
        yield 'Small doc' => [
            'documents' => array_fill(0, 9_999, Data::readJsonFile(Data::SMALL_FILE_PATH)),
        ];

        yield 'Small BSON doc' => [
            'documents' => array_fill(0, 9_999, Document::fromJSON(file_get_contents(Data::SMALL_FILE_PATH))),
        ];

        yield 'Large doc' => [
            'documents' => array_fill(0, 9, Data::readJsonFile(Data::LARGE_FILE_PATH)),
        ];

        yield 'Large BSON doc' => [
            'documents' => array_fill(0, 9, Document::fromJSON(file_get_contents(Data::LARGE_FILE_PATH))),
        ];
    }
}
