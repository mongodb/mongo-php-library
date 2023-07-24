<?php

namespace MongoDB\Tests;

use Generator;

/** @runTestsInSeparateProcesses */
final class ExamplesTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Examples are not tested on sharded clusters.');
        }

        if ($this->isApiVersionRequired()) {
            $this->markTestSkipped('Examples are not tested with when the server requires specifying an API version.');
        }

        self::createTestClient()->dropDatabase('test');
    }

    public function dataExamples(): Generator
    {
        $expectedOutput = <<<'OUTPUT'
{ "_id" : null, "totalCount" : 100, "evenCount" : %d, "oddCount" : %d, "maxValue" : %d, "minValue" : %d }
OUTPUT;

        yield 'aggregate' => [
            'file' => __DIR__ . '/../examples/aggregate.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
%s
%s
%s
%s
%s
%s
%s
%s
OUTPUT;

        yield 'bulk' => [
            'file' => __DIR__ . '/../examples/bulk.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
drop command started
command: %s

drop command %a

insert command started
command: %s

insert command succeeded
reply: %s

update command started
command: %s

update command succeeded
reply: %s

find command started
command: %s

find command succeeded
reply: %s

%s
%s
getMore command started
command: %s

getMore command succeeded
reply: %s

%s
OUTPUT;

        yield 'command_logger' => [
            'file' => __DIR__ . '/../examples/command_logger.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Examples\Persistable\PersistableEntry Object
(
    [id:MongoDB\Examples\Persistable\PersistableEntry:private] => MongoDB\BSON\ObjectId Object
        (
            [oid] => %s
        )

    [name] => alcaeus
    [emails] => Array
        (
            [0] => MongoDB\Examples\Persistable\PersistableEmail Object
                (
                    [type] => work
                    [address] => alcaeus@example.com
                )

            [1] => MongoDB\Examples\Persistable\PersistableEmail Object
                (
                    [type] => private
                    [address] => secret@example.com
                )

        )

)
OUTPUT;

        yield 'persistable' => [
            'file' => __DIR__ . '/../examples/persistable.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Examples\Typemap\TypeMapEntry Object
(
    [id:MongoDB\Examples\Typemap\TypeMapEntry:private] => MongoDB\BSON\ObjectId Object
        (
            [oid] => %s
        )

    [name:MongoDB\Examples\Typemap\TypeMapEntry:private] => alcaeus
    [emails:MongoDB\Examples\Typemap\TypeMapEntry:private] => Array
        (
            [0] => MongoDB\Examples\Typemap\TypeMapEmail Object
                (
                    [type:MongoDB\Examples\Typemap\TypeMapEmail:private] => work
                    [address:MongoDB\Examples\Typemap\TypeMapEmail:private] => alcaeus@example.com
                )

            [1] => MongoDB\Examples\Typemap\TypeMapEmail Object
                (
                    [type:MongoDB\Examples\Typemap\TypeMapEmail:private] => private
                    [address:MongoDB\Examples\Typemap\TypeMapEmail:private] => secret@example.com
                )

        )

)
OUTPUT;

        yield 'typemap' => [
            'file' => __DIR__ . '/../examples/typemap.php',
            'expectedOutput' => $expectedOutput,
        ];
    }

    public function testChangeStream(): void
    {
        $this->skipIfChangeStreamIsNotSupported();

        $expectedOutput = <<<'OUTPUT'
%s
%s
%s
%s
%s
%s
%s
%s
%s
%s
Aborting after 3 seconds...
OUTPUT;

        $this->testExample(__DIR__ . '/../examples/changestream.php', $expectedOutput);
    }

    /** @dataProvider dataExamples */
    public function testExample(string $file, string $expectedOutput): void
    {
        require $file;

        self::assertStringMatchesFormat($expectedOutput, $this->getActualOutputForAssertion());
    }

    public function testWithTransaction(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $expectedOutput = <<<'OUTPUT'
%s
%s
%s
OUTPUT;

        $this->testExample(__DIR__ . '/../examples/with_transaction.php', $expectedOutput);
    }
}
