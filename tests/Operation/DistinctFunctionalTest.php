<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\Distinct;
use MongoDB\Tests\CommandObserver;

use function is_scalar;
use function json_encode;
use function usort;

class DistinctFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    [],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOption(array $typeMap, array $expectedDocuments): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);
        $bulkWrite->insert([
            'x' => (object) ['foo' => 'bar'],
        ]);
        $bulkWrite->insert(['x' => 4]);
        $bulkWrite->insert([
            'x' => (object) ['foo' => ['foo' => 'bar']],
        ]);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $distinct = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', [], ['typeMap' => $typeMap]);
        $values = $distinct->execute($this->getPrimaryServer());

        /* This sort callable sorts all scalars to the front of the list. All
         * non-scalar values are sorted by running json_encode on them and
         * comparing their string representations.
         */
        $sort = function ($a, $b) {
            if (is_scalar($a) && ! is_scalar($b)) {
                return -1;
            }

            if (! is_scalar($a)) {
                if (is_scalar($b)) {
                    return 1;
                }

                $a = json_encode($a);
                $b = json_encode($b);
            }

            return $a < $b ? -1 : 1;
        };

        usort($expectedDocuments, $sort);
        usort($values, $sort);

        $this->assertEquals($expectedDocuments, $values);
    }

    public function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            'No type map' => [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => ['foo' => 'bar']],
                ],
            ],
            'array/array' => [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => ['foo' => 'bar']],
                ],
            ],
            'object/array' => [
                ['root' => 'object', 'document' => 'array'],
                [
                    (object) ['foo' => 'bar'],
                    4,
                    (object) ['foo' => ['foo' => 'bar']],
                ],
            ],
            'array/stdClass' => [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => (object) ['foo' => 'bar']],
                ],
            ],
        ];
    }
}
