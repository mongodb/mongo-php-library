<?php

namespace MongoDB\Benchmark\DriverBench;

use Generator;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\BSON\Document;
use PhpBench\Attributes\ParamProviders;

use function file_get_contents;

/**
 * BSON micro-benchmarks
 *
 * @see https://github.com/mongodb/specifications/blob/e09b41df206f9efaa36ba4c332c47d04ddb7d6d1/source/benchmarking/benchmarking.rst#bson-micro-benchmarks
 */
final class BSONMicroBench
{
    /** @param array{document:Document} $params */
    #[ParamProviders('provideParams')]
    public function benchEncoding(array $params): void
    {
        $document = $params['document'];
        for ($i = 0; $i < 10_000; $i++) {
            $document->__toString();
        }
    }

    /** @param array{bson:string} $params */
    #[ParamProviders('provideParams')]
    public function benchDecoding(array $params): void
    {
        $bson = $params['bson'];
        for ($i = 0; $i < 10_000; $i++) {
            Document::fromBSON($bson);
        }
    }

    public static function provideParams(): Generator
    {
        $cases = [
            'flat' => Data::FLAT_BSON_PATH,
            'deep' => Data::DEEP_BSON_PATH,
            'full' => Data::FULL_BSON_PATH,
        ];

        foreach ($cases as $name => $path) {
            yield $name => [
                'document' => $document = Document::fromJSON(file_get_contents($path)),
                'bson' => (string) $document,
            ];
        }
    }
}
