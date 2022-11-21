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
object(MongoDB\Examples\PersistableEntry)#%d (%d) {
  ["id":"MongoDB\Examples\PersistableEntry":private]=>
  object(MongoDB\BSON\ObjectId)#%d (%d) {
    ["oid"]=>
    string(24) "%s"
  }
  ["name"]=>
  string(7) "alcaeus"
  ["emails"]=>
  array(2) {
    [0]=>
    object(MongoDB\Examples\PersistableEmail)#%d (%d) {
      ["type"]=>
      string(4) "work"
      ["address"]=>
      string(19) "alcaeus@example.com"
    }
    [1]=>
    object(MongoDB\Examples\PersistableEmail)#%d (%d) {
      ["type"]=>
      string(7) "private"
      ["address"]=>
      string(18) "secret@example.com"
    }
  }
}
OUTPUT;

        yield 'persistable' => [
            'file' => __DIR__ . '/../examples/persistable.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
object(MongoDB\Examples\TypeMapEntry)#%d (%d) {
  ["id":"MongoDB\Examples\TypeMapEntry":private]=>
  object(MongoDB\BSON\ObjectId)#%d (%d) {
    ["oid"]=>
    string(24) "%s"
  }
  ["name":"MongoDB\Examples\TypeMapEntry":private]=>
  string(7) "alcaeus"
  ["emails":"MongoDB\Examples\TypeMapEntry":private]=>
  array(2) {
    [0]=>
    object(MongoDB\Examples\TypeMapEmail)#%d (%d) {
      ["type":"MongoDB\Examples\TypeMapEmail":private]=>
      string(4) "work"
      ["address":"MongoDB\Examples\TypeMapEmail":private]=>
      string(19) "alcaeus@example.com"
    }
    [1]=>
    object(MongoDB\Examples\TypeMapEmail)#%d (%d) {
      ["type":"MongoDB\Examples\TypeMapEmail":private]=>
      string(7) "private"
      ["address":"MongoDB\Examples\TypeMapEmail":private]=>
      string(18) "secret@example.com"
    }
  }
}
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
