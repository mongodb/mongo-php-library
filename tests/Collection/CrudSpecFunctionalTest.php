<?php

namespace MongoDB\Tests\Collection;

use IteratorIterator;
use LogicException;
use MongoDB\BulkWriteResult;
use MongoDB\Collection;
use MongoDB\DeleteResult;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\InsertManyResult;
use MongoDB\InsertOneResult;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\UpdateResult;
use MultipleIterator;
use PHPUnit_Framework_SkippedTestError;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function array_diff_key;
use function array_key_exists;
use function array_map;
use function file_get_contents;
use function glob;
use function json_decode;
use function MongoDB\is_last_pipeline_operator_write;
use function sprintf;
use function str_replace;
use function strtolower;
use function version_compare;

/**
 * CRUD spec functional tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class CrudSpecFunctionalTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var Collection */
    private $expectedCollection;

    private function doSetUp()
    {
        parent::setUp();

        $this->expectedCollection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName() . '.expected');
        $this->expectedCollection->drop();
    }

    /**
     * @dataProvider provideSpecificationTests
     */
    public function testSpecification(array $initialData, array $test, $minServerVersion, $maxServerVersion)
    {
        if (isset($minServerVersion) || isset($maxServerVersion)) {
            $this->checkServerVersion($minServerVersion, $maxServerVersion);
        }

        $expectedData = isset($test['outcome']['collection']['data']) ? $test['outcome']['collection']['data'] : null;
        $this->initializeData($initialData, $expectedData);

        if (isset($test['outcome']['collection']['name'])) {
            $outputCollection = new Collection($this->manager, $this->getDatabaseName(), $test['outcome']['collection']['name']);
            $outputCollection->drop();
        }

        $result = null;
        $exception = null;

        try {
            $result = $this->executeOperation($test['operation']);
        } catch (RuntimeException $e) {
            $exception = $e;
        }

        $this->executeOutcome($test['operation'], $test['outcome'], $result, $exception);
    }

    public function provideSpecificationTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/spec-tests/*/*.json') as $filename) {
            $json = json_decode(file_get_contents($filename), true);

            $minServerVersion = isset($json['minServerVersion']) ? $json['minServerVersion'] : null;
            $maxServerVersion = isset($json['maxServerVersion']) ? $json['maxServerVersion'] : null;

            foreach ($json['tests'] as $test) {
                $name = str_replace(' ', '_', $test['description']);
                $testArgs[$name] = [$json['data'], $test, $minServerVersion, $maxServerVersion];
            }
        }

        return $testArgs;
    }

    /**
     * Assert that the collections contain equivalent documents.
     *
     * @param Collection $expectedCollection
     * @param Collection $actualCollection
     */
    private function assertEquivalentCollections($expectedCollection, $actualCollection)
    {
        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new IteratorIterator($expectedCollection->find()));
        $mi->attachIterator(new IteratorIterator($actualCollection->find()));

        foreach ($mi as $documents) {
            list($expectedDocument, $actualDocument) = $documents;
            $this->assertSameDocument($expectedDocument, $actualDocument);
        }
    }

    /**
     * Checks that the server version is within the allowed bounds (if any).
     *
     * @param string|null $minServerVersion
     * @param string|null $maxServerVersion
     * @throws PHPUnit_Framework_SkippedTestError
     */
    private function checkServerVersion($minServerVersion, $maxServerVersion)
    {
        $serverVersion = $this->getServerVersion();

        if (isset($minServerVersion) && version_compare($serverVersion, $minServerVersion, '<')) {
            $this->markTestSkipped(sprintf('Server version "%s" < minServerVersion "%s"', $serverVersion, $minServerVersion));
        }

        if (isset($maxServerVersion) && version_compare($serverVersion, $maxServerVersion, '>=')) {
            $this->markTestSkipped(sprintf('Server version "%s" >= maxServerVersion "%s"', $serverVersion, $maxServerVersion));
        }
    }

    /**
     * Executes an "operation" block.
     *
     * @param array $operation
     * @return mixed
     * @throws LogicException if the operation is unsupported
     */
    private function executeOperation(array $operation)
    {
        switch ($operation['name']) {
            case 'aggregate':
                return $this->collection->aggregate(
                    $operation['arguments']['pipeline'],
                    array_diff_key($operation['arguments'], ['pipeline' => 1])
                );
            case 'bulkWrite':
                return $this->collection->bulkWrite(
                    array_map([$this, 'prepareBulkWriteRequest'], $operation['arguments']['requests']),
                    isset($operation['arguments']['options']) ? $operation['arguments']['options'] : []
                );
            case 'count':
            case 'countDocuments':
            case 'find':
                return $this->collection->{$operation['name']}(
                    isset($operation['arguments']['filter']) ? $operation['arguments']['filter'] : [],
                    array_diff_key($operation['arguments'], ['filter' => 1])
                );
            case 'estimatedDocumentCount':
                return $this->collection->estimatedDocumentCount($operation['arguments']);
            case 'deleteMany':
            case 'deleteOne':
            case 'findOneAndDelete':
                return $this->collection->{$operation['name']}(
                    $operation['arguments']['filter'],
                    array_diff_key($operation['arguments'], ['filter' => 1])
                );
            case 'distinct':
                return $this->collection->distinct(
                    $operation['arguments']['fieldName'],
                    isset($operation['arguments']['filter']) ? $operation['arguments']['filter'] : [],
                    array_diff_key($operation['arguments'], ['fieldName' => 1, 'filter' => 1])
                );
            case 'findOneAndReplace':
                $operation['arguments'] = $this->prepareFindAndModifyArguments($operation['arguments']);
                // Fall through

            case 'replaceOne':
                return $this->collection->{$operation['name']}(
                    $operation['arguments']['filter'],
                    $operation['arguments']['replacement'],
                    array_diff_key($operation['arguments'], ['filter' => 1, 'replacement' => 1])
                );
            case 'findOneAndUpdate':
                $operation['arguments'] = $this->prepareFindAndModifyArguments($operation['arguments']);
                // Fall through

            case 'updateMany':
            case 'updateOne':
                return $this->collection->{$operation['name']}(
                    $operation['arguments']['filter'],
                    $operation['arguments']['update'],
                    array_diff_key($operation['arguments'], ['filter' => 1, 'update' => 1])
                );
            case 'insertMany':
                return $this->collection->insertMany(
                    $operation['arguments']['documents'],
                    isset($operation['arguments']['options']) ? $operation['arguments']['options'] : []
                );
            case 'insertOne':
                return $this->collection->insertOne(
                    $operation['arguments']['document'],
                    array_diff_key($operation['arguments'], ['document' => 1])
                );
            default:
                throw new LogicException('Unsupported operation: ' . $operation['name']);
        }
    }

    /**
     * Executes an "outcome" block.
     *
     * @param array            $operation
     * @param array            $outcome
     * @param mixed            $result
     * @param RuntimeException $exception
     * @return mixed
     * @throws LogicException if the operation is unsupported
     */
    private function executeOutcome(array $operation, array $outcome, $result, RuntimeException $exception = null)
    {
        $expectedError = array_key_exists('error', $outcome) ? $outcome['error'] : false;

        if ($expectedError) {
            $this->assertNull($result);
            $this->assertNotNull($exception);

            $result = $this->extractResultFromException($operation, $outcome, $exception);
        }

        if (array_key_exists('result', $outcome)) {
            $this->executeAssertResult($operation, $outcome['result'], $result);
        }

        if (isset($outcome['collection'])) {
            $actualCollection = isset($outcome['collection']['name'])
                ? new Collection($this->manager, $this->getDatabaseName(), $outcome['collection']['name'])
                : $this->collection;

            $this->assertEquivalentCollections($this->expectedCollection, $actualCollection);
        }
    }

    /**
     * Extracts a result from an exception.
     *
     * Errors for bulkWrite and insertMany operations may still report a write
     * result. This method will attempt to extract such a result so that it can
     * be used in executeAssertResult().
     *
     * If no result can be extracted, null will be returned.
     *
     * @param array            $operation
     * @param RuntimeException $exception
     * @return mixed
     */
    private function extractResultFromException(array $operation, array $outcome, RuntimeException $exception)
    {
        switch ($operation['name']) {
            case 'bulkWrite':
                $insertedIds = isset($outcome['result']['insertedIds']) ? $outcome['result']['insertedIds'] : [];

                if ($exception instanceof BulkWriteException) {
                    return new BulkWriteResult($exception->getWriteResult(), $insertedIds);
                }
                break;

            case 'insertMany':
                $insertedIds = isset($outcome['result']['insertedIds']) ? $outcome['result']['insertedIds'] : [];

                if ($exception instanceof BulkWriteException) {
                    return new InsertManyResult($exception->getWriteResult(), $insertedIds);
                }
                break;
        }

        return null;
    }

    /**
     * Executes the "result" section of an "outcome" block.
     *
     * @param array $operation
     * @param mixed $expectedResult
     * @param mixed $actualResult
     * @throws LogicException if the operation is unsupported
     */
    private function executeAssertResult(array $operation, $expectedResult, $actualResult)
    {
        switch ($operation['name']) {
            case 'aggregate':
                /* Returning a cursor for the $out collection is optional per
                 * the CRUD specification and is not implemented in the library
                 * since we have no concept of lazy cursors. We will not assert
                 * the result here; however, assertEquivalentCollections() will
                 * assert the output collection's contents later.
                 */
                if (! is_last_pipeline_operator_write($operation['arguments']['pipeline'])) {
                    $this->assertSameDocuments($expectedResult, $actualResult);
                }
                break;

            case 'bulkWrite':
                $this->assertIsArray($expectedResult);
                $this->assertInstanceOf(BulkWriteResult::class, $actualResult);

                if (isset($expectedResult['deletedCount'])) {
                    $this->assertSame($expectedResult['deletedCount'], $actualResult->getDeletedCount());
                }

                if (isset($expectedResult['insertedCount'])) {
                    $this->assertSame($expectedResult['insertedCount'], $actualResult->getInsertedCount());
                }

                if (isset($expectedResult['insertedIds'])) {
                    $this->assertSameDocument(
                        ['insertedIds' => $expectedResult['insertedIds']],
                        ['insertedIds' => $actualResult->getInsertedIds()]
                    );
                }

                if (isset($expectedResult['matchedCount'])) {
                    $this->assertSame($expectedResult['matchedCount'], $actualResult->getMatchedCount());
                }

                if (isset($expectedResult['modifiedCount'])) {
                    $this->assertSame($expectedResult['modifiedCount'], $actualResult->getModifiedCount());
                }

                if (isset($expectedResult['upsertedCount'])) {
                    $this->assertSame($expectedResult['upsertedCount'], $actualResult->getUpsertedCount());
                }

                if (isset($expectedResult['upsertedIds'])) {
                    $this->assertSameDocument(
                        ['upsertedIds' => $expectedResult['upsertedIds']],
                        ['upsertedIds' => $actualResult->getUpsertedIds()]
                    );
                }
                break;

            case 'count':
            case 'countDocuments':
            case 'estimatedDocumentCount':
                $this->assertSame($expectedResult, $actualResult);
                break;

            case 'distinct':
                $this->assertSameDocument(
                    ['values' => $expectedResult],
                    ['values' => $actualResult]
                );
                break;

            case 'find':
                $this->assertSameDocuments($expectedResult, $actualResult);
                break;

            case 'deleteMany':
            case 'deleteOne':
                $this->assertIsArray($expectedResult);
                $this->assertInstanceOf(DeleteResult::class, $actualResult);

                if (isset($expectedResult['deletedCount'])) {
                    $this->assertSame($expectedResult['deletedCount'], $actualResult->getDeletedCount());
                }
                break;

            case 'findOneAndDelete':
            case 'findOneAndReplace':
            case 'findOneAndUpdate':
                $this->assertSameDocument(
                    ['result' => $expectedResult],
                    ['result' => $actualResult]
                );
                break;

            case 'insertMany':
                $this->assertIsArray($expectedResult);
                $this->assertInstanceOf(InsertManyResult::class, $actualResult);

                if (isset($expectedResult['insertedCount'])) {
                    $this->assertSame($expectedResult['insertedCount'], $actualResult->getInsertedCount());
                }

                if (isset($expectedResult['insertedIds'])) {
                    $this->assertSameDocument(
                        ['insertedIds' => $expectedResult['insertedIds']],
                        ['insertedIds' => $actualResult->getInsertedIds()]
                    );
                }
                break;

            case 'insertOne':
                $this->assertIsArray($expectedResult);
                $this->assertInstanceOf(InsertOneResult::class, $actualResult);

                if (isset($expectedResult['insertedCount'])) {
                    $this->assertSame($expectedResult['insertedCount'], $actualResult->getInsertedCount());
                }

                if (isset($expectedResult['insertedId'])) {
                    $this->assertSameDocument(
                        ['insertedId' => $expectedResult['insertedId']],
                        ['insertedId' => $actualResult->getInsertedId()]
                    );
                }
                break;

            case 'replaceOne':
            case 'updateMany':
            case 'updateOne':
                $this->assertIsArray($expectedResult);
                $this->assertInstanceOf(UpdateResult::class, $actualResult);

                if (isset($expectedResult['matchedCount'])) {
                    $this->assertSame($expectedResult['matchedCount'], $actualResult->getMatchedCount());
                }

                if (isset($expectedResult['modifiedCount'])) {
                    $this->assertSame($expectedResult['modifiedCount'], $actualResult->getModifiedCount());
                }

                if (isset($expectedResult['upsertedCount'])) {
                    $this->assertSame($expectedResult['upsertedCount'], $actualResult->getUpsertedCount());
                }

                if (array_key_exists('upsertedId', $expectedResult)) {
                    $this->assertSameDocument(
                        ['upsertedId' => $expectedResult['upsertedId']],
                        ['upsertedId' => $actualResult->getUpsertedId()]
                    );
                }
                break;

            default:
                throw new LogicException('Unsupported operation: ' . $operation['name']);
        }
    }

    /**
     * Initializes data in the test collections.
     *
     * @param array $initialData
     * @param array $expectedData
     */
    private function initializeData(array $initialData, array $expectedData = null)
    {
        if (! empty($initialData)) {
            $this->collection->insertMany($initialData);
        }

        if (! empty($expectedData)) {
            $this->expectedCollection->insertMany($expectedData);
        }
    }

    /**
     * Prepares a request element for a bulkWrite operation.
     *
     * @param array $request
     * @return array
     */
    private function prepareBulkWriteRequest(array $request)
    {
        switch ($request['name']) {
            case 'deleteMany':
            case 'deleteOne':
                return [
                    $request['name'] => [
                        $request['arguments']['filter'],
                        array_diff_key($request['arguments'], ['filter' => 1]),
                    ],
                ];
            case 'insertOne':
                return [ 'insertOne' => [ $request['arguments']['document'] ]];
            case 'replaceOne':
                return [
                    'replaceOne' => [
                        $request['arguments']['filter'],
                        $request['arguments']['replacement'],
                        array_diff_key($request['arguments'], ['filter' => 1, 'replacement' => 1]),
                    ],
                ];
            case 'updateMany':
            case 'updateOne':
                return [
                    $request['name'] => [
                        $request['arguments']['filter'],
                        $request['arguments']['update'],
                        array_diff_key($request['arguments'], ['filter' => 1, 'update' => 1]),
                    ],
                ];
            default:
                throw new LogicException('Unsupported bulk write request: ' . $request['name']);
        }
    }

    /**
     * Prepares arguments for findOneAndReplace and findOneAndUpdate operations.
     *
     * @param array $arguments
     * @return array
     */
    private function prepareFindAndModifyArguments(array $arguments)
    {
        if (isset($arguments['returnDocument'])) {
            $arguments['returnDocument'] = 'after' === strtolower($arguments['returnDocument'])
                ? FindOneAndReplace::RETURN_DOCUMENT_AFTER
                : FindOneAndReplace::RETURN_DOCUMENT_BEFORE;
        }

        return $arguments;
    }
}
