<?php

namespace MongoDB\Tests;

use Generator;

/** @runTestsInSeparateProcesses */
final class ExamplesTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

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
{ "_id" : { "$oid" : "%s" }, "x" : 0 }
{ "_id" : { "$oid" : "%s" }, "y" : 1 }
{ "_id" : { "$oid" : "%s" }, "x" : 2, "y" : 2 }
{ "_id" : { "$oid" : "%s" }, "x" : 3 }
{ "_id" : { "$oid" : "%s" }, "x" : 5 }
{ "_id" : { "$oid" : "%s" }, "x" : 6, "updateMany" : true }
{ "_id" : { "$oid" : "%s" }, "x" : 7, "updateMany" : true }
{ "_id" : { "$oid" : "%s" }, "x" : 10 }
OUTPUT;

        yield 'bulk' => [
            'file' => __DIR__ . '/../examples/bulk.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
drop command started
command: { "drop" : "coll", "$db" : "test", "lsid" : { %s }%S }

drop command failed
reply: { "ok" : 0.0, "errmsg" : "ns not found", "code" : 26, "codeName" : "NamespaceNotFound"%S }
exception: MongoDB\Driver\Exception\ServerException
exception.code: 26
exception.message: ns not found

insert command started
command: { "insert" : "coll", "ordered" : true, "$db" : "test", "lsid" : { %s }%S, "documents" : [ { "x" : 1, "_id" : { "$oid" : "%s" } }, { "x" : 2, "_id" : { "$oid" : "%s" } }, { "x" : 3, "_id" : { "$oid" : "%s" } } ] }

insert command succeeded
reply: { "n" : 3, %s }

update command started
command: { "update" : "coll", "ordered" : true, "$db" : "test", "lsid" : { %s }%S, "updates" : [ { "q" : { "x" : { "$gt" : 1 } }, "u" : { "$set" : { "y" : 1 } }, "upsert" : false, "multi" : true } ] }

update command succeeded
reply: { "n" : 2, %s }

find command started
command: { "find" : "coll", "filter" : {  }, "batchSize" : 2, "$db" : "test", "lsid" : { %s }%S }

find command succeeded
reply: { "cursor" : { "firstBatch" : [ { "_id" : { "$oid" : "%s" }, "x" : 1 }, { "_id" : { "$oid" : "%s" }, "x" : 2, "y" : 1 } ], "id" : %d, "ns" : "test.coll" }, %s }

{ "_id" : { "$oid" : "%s" }, "x" : 1 }
{ "_id" : { "$oid" : "%s" }, "x" : 2, "y" : 1 }
getMore command started
command: { "getMore" : %d, "collection" : "coll", "batchSize" : 2, "$db" : "test", "lsid" : { %s }%S }

getMore command succeeded
reply: { "cursor" : { "nextBatch" : [ { "_id" : { "$oid" : "%s" }, "x" : 3, "y" : 1 } ], "id" : 0, "ns" : "test.coll" }, %s }

{ "_id" : { "$oid" : "%s" }, "x" : 3, "y" : 1 }
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
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 0 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 1 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 2 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 3 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 4 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 5 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 6 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 7 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 8 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
{ "_id" : { "_data" : "%s" }, "operationType" : "insert", "clusterTime" : { "$timestamp" : { "t" : %d, "i" : %d } }%S, "fullDocument" : { "_id" : { "$oid" : "%s" }, "x" : 9 }, "ns" : { "db" : "test", "coll" : "coll" }, "documentKey" : { "_id" : { "$oid" : "%s" } } }
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
{ "_id" : { "$oid" : "%s" }, "x" : 1 }
{ "_id" : { "$oid" : "%s" }, "x" : 2, "y" : 1 }
{ "_id" : { "$oid" : "%s" }, "x" : 3, "y" : 1 }
OUTPUT;

        $this->testExample(__DIR__ . '/../examples/with_transaction.php', $expectedOutput);
    }
}
