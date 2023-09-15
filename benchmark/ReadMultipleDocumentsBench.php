<?php

namespace MongoDB\Benchmark;

use Generator;
use MongoDB\Benchmark\Fixtures\Data;
use MongoDB\Benchmark\Fixtures\PassThruCodec;
use MongoDB\Benchmark\Fixtures\ToObjectCodec;
use MongoDB\BSON\Document;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Warmup;

use function array_fill;
use function array_intersect_key;
use function assert;
use function is_array;
use function is_object;
use function sprintf;

#[BeforeClassMethods('prepareDatabase')]
#[Warmup(1)]
final class ReadMultipleDocumentsBench
{
    public static function prepareDatabase(): void
    {
        $collection = Utils::getCollection();
        $collection->drop();

        $tweet = Data::readJsonFile(Data::TWEET_FILE_PATH);

        $documents = array_fill(0, 1000, $tweet);

        $collection->insertMany($documents);
    }

    public function provideParams(): Generator
    {
        yield 'Driver default typemap' => [
            'codec' => null,
            'typeMap' => [],
            'accessor' => 'object',
        ];

        yield 'Array typemap' => [
            'codec' => null,
            'typeMap' => ['root' => 'array', 'array' => 'array', 'document' => 'array'],
            'accessor' => 'array',
        ];

        yield 'Library default typemap' => [
            'codec' => null,
            'typeMap' => [
                'array' => BSONArray::class,
                'document' => BSONDocument::class,
                'root' => BSONDocument::class,
            ],
            'accessor' => 'object',
        ];

        yield 'Raw BSON' => [
            'codec' => null,
            'typeMap' => ['root' => 'bson'],
            'accessor' => 'bson',
        ];

        yield 'Codec (pass thru)' => [
            'codec' => new PassThruCodec(),
            'typeMap' => null,
            'accessor' => 'bson',
        ];

        yield 'Codec (to object)' => [
            'codec' => new ToObjectCodec(),
            'typeMap' => null,
            'accessor' => 'object',
        ];
    }

    #[ParamProviders('provideParams')]
    public function benchCursorToArray(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = Utils::getCollection();

        $collection->find([], $options)->toArray();
    }

    #[ParamProviders('provideParams')]
    public function benchAccessId(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = Utils::getCollection();

        // Exhaust cursor and access identifier on each document
        foreach ($collection->find([], $options) as $document) {
            $this->accessId($document, $params['accessor']);
        }
    }

    #[ParamProviders('provideParams')]
    public function benchAccessNestedItem(array $params): void
    {
        $options = array_intersect_key($params, ['codec' => true, 'typeMap' => true]);

        $collection = Utils::getCollection();

        // Exhaust cursor and access identifier on each document
        foreach ($collection->find([], $options) as $document) {
            $this->accessNestedItem($document, $params['accessor']);
        }
    }

    /** @param array|object $document */
    private function accessId($document, string $accessor): void
    {
        switch ($accessor) {
            case 'array':
                assert(is_array($document));
                $document['_id'];
                break;

            case 'bson':
                assert($document instanceof Document);
                $document->get('_id');
                break;

            case 'object':
                assert(is_object($document));
                $document->_id;
                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid accessor "%s"', $accessor));
        }
    }

    /** @param array|object $document */
    private function accessNestedItem($document, string $accessor): void
    {
        switch ($accessor) {
            case 'array':
                assert(is_array($document));
                $document['entities']['user_mentions'][0]['screen_name'];
                break;

            case 'bson':
                assert($document instanceof Document);
                $document->get('entities')->get('user_mentions')->get(0)->get('screen_name');
                break;

            case 'object':
                assert(is_object($document));
                $document->entities->user_mentions[0]->screen_name;
                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid accessor "%s"', $accessor));
        }
    }
}
