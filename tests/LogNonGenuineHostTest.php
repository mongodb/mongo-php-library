<?php

namespace MongoDB\Tests;

use MongoDB\Client;
use MongoDB\Driver\Exception\InvalidArgumentException;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

use function func_get_args;
use function MongoDB\add_logger;
use function MongoDB\remove_logger;

/** @see https://jira.mongodb.org/browse/DRIVERS-2583 */
class LogNonGenuineHostTest extends TestCase
{
    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->logger = $this->createTestPsrLogger();

        add_logger($this->logger);
    }

    public function tearDown(): void
    {
        remove_logger($this->logger);
    }

    /** @dataProvider provideCosmosUris */
    public function testCosmosUriLogsInfoMessage(string $uri): void
    {
        $this->createClientAndIgnoreSrvLookupError($uri);

        $expectedLog = [
            'info',
            'You appear to be connected to a CosmosDB cluster. For more information regarding feature compatibility and support please visit https://www.mongodb.com/supportability/cosmosdb',
            ['domain' => 'mongoc'],
        ];

        $this->assertContains($expectedLog, $this->logger->logs);
    }

    public static function provideCosmosUris(): array
    {
        return [
            ['mongodb://a.mongo.cosmos.azure.com:19555/'],
            ['mongodb://a.MONGO.COSMOS.AZURE.COM:19555/'],
            ['mongodb+srv://a.mongo.cosmos.azure.com/'],
            ['mongodb+srv://A.MONGO.COSMOS.AZURE.COM/'],
            // Mixing genuine and nongenuine hosts (unlikely in practice)
            ['mongodb://a.example.com:27017,b.mongo.cosmos.azure.com:19555/'],
        ];
    }

    /** @dataProvider provideDocumentDbUris */
    public function testDocumentDbUriLogsInfoMessage(string $uri): void
    {
        $this->createClientAndIgnoreSrvLookupError($uri);

        $expectedLog = [
            'info',
            'You appear to be connected to a DocumentDB cluster. For more information regarding feature compatibility and support please visit https://www.mongodb.com/supportability/documentdb',
            ['domain' => 'mongoc'],
        ];

        $this->assertContains($expectedLog, $this->logger->logs);
    }

    public static function provideDocumentDbUris(): array
    {
        return [
            ['mongodb://a.docdb.amazonaws.com:27017/'],
            ['mongodb://a.docdb-elastic.amazonaws.com:27017/'],
            ['mongodb://a.DOCDB.AMAZONAWS.COM:27017/'],
            ['mongodb://a.DOCDB-ELASTIC.AMAZONAWS.COM:27017/'],
            ['mongodb+srv://a.DOCDB.AMAZONAWS.COM/'],
            ['mongodb+srv://a.DOCDB-ELASTIC.AMAZONAWS.COM/'],
            // Mixing genuine and nongenuine hosts (unlikely in practice)
            ['mongodb://a.example.com:27017,b.docdb.amazonaws.com:27017/'],
            ['mongodb://a.example.com:27017,b.docdb-elastic.amazonaws.com:27017/'],
        ];
    }

    /** @dataProvider provideGenuineUris */
    public function testGenuineUriDoesNotLog(string $uri): void
    {
        $this->createClientAndIgnoreSrvLookupError($uri);
        $this->assertEmpty($this->logger->logs);
    }

    public static function provideGenuineUris(): array
    {
        return [
            ['mongodb://a.example.com:27017,b.example.com:27017/'],
            ['mongodb://a.mongodb.net:27017'],
            ['mongodb+srv://a.example.com/'],
            ['mongodb+srv://a.mongodb.net/'],
            // Host names do not end with expected suffix
            ['mongodb://a.mongo.cosmos.azure.com.tld:19555/'],
            ['mongodb://a.docdb.amazonaws.com.tld:27017/'],
            ['mongodb://a.docdb-elastic.amazonaws.com.tld:27017/'],
            // SRV host names do not end with expected suffix
            ['mongodb+srv://a.mongo.cosmos.azure.com.tld/'],
            ['mongodb+srv://a.docdb.amazonaws.com.tld/'],
            ['mongodb+srv://a.docdb-elastic.amazonaws.com.tld/'],
        ];
    }

    private function createClientAndIgnoreSrvLookupError(string $uri): void
    {
        try {
            $client = new Client($uri);
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Failed to look up SRV record', $e->getMessage());
        }
    }

    private function createTestPsrLogger(): LoggerInterface
    {
        return new class extends AbstractLogger {
            public $logs = [];

            /**
             * Note: parameter type hints are omitted for compatibility with
             * psr/log 1.1.4 and PHP 7.4.
             */
            public function log($level, $message, array $context = []): void
            {
                /* Ignore debug-level log messages from PHPC (e.g. connection
                 * string, Manager creation, handshake data). */
                if ($level === 'debug') {
                    return;
                }

                $this->logs[] = func_get_args();
            }
        };
    }
}
