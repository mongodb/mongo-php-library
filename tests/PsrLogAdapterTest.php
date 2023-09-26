<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Monitoring\LogSubscriber;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\PsrLogAdapter;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

use function func_get_args;
use function MongoDB\add_logger;
use function MongoDB\Driver\Monitoring\mongoc_log;
use function MongoDB\remove_logger;
use function sprintf;

class PsrLogAdapterTest extends BaseTestCase
{
    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->logger = $this->createTestPsrLogger();

        PsrLogAdapter::addLogger($this->logger);
    }

    public function tearDown(): void
    {
        PsrLogAdapter::removeLogger($this->logger);
    }

    public function testAddAndRemoveLoggerFunctions(): void
    {
        $logger = $this->createTestPsrLogger();

        mongoc_log(LogSubscriber::LEVEL_INFO, 'domain1', 'info1');
        PsrLogAdapter::writeLog(PsrLogAdapter::INFO, 'domain2', 'info2');

        add_logger($logger);

        mongoc_log(LogSubscriber::LEVEL_INFO, 'domain3', 'info3');
        PsrLogAdapter::writeLog(PsrLogAdapter::INFO, 'domain4', 'info4');

        remove_logger($logger);

        mongoc_log(LogSubscriber::LEVEL_INFO, 'domain5', 'info5');
        PsrLogAdapter::writeLog(PsrLogAdapter::INFO, 'domain6', 'info6');

        $expectedLogs = [
            [LogLevel::INFO, 'info3', ['domain' => 'domain3']],
            [LogLevel::INFO, 'info4', ['domain' => 'domain4']],
        ];

        $this->assertSame($expectedLogs, $logger->logs);
    }

    public function testLog(): void
    {
        /* This uses PHPC's internal mongoc_log() function to write messages
         * directly to libmongoc. Those messages are then relayed to
         * PsrLogAdapter and forwarded to each registered PSR logger.
         *
         * Note: it's not possible to test PsrLogAdapter::log() with an invalid
         * level since mongoc_log() already validates its level parameter. */
        mongoc_log(LogSubscriber::LEVEL_ERROR, 'domain1', 'error');
        mongoc_log(LogSubscriber::LEVEL_CRITICAL, 'domain2', 'critical');
        mongoc_log(LogSubscriber::LEVEL_WARNING, 'domain3', 'warning');
        mongoc_log(LogSubscriber::LEVEL_MESSAGE, 'domain4', 'message');
        mongoc_log(LogSubscriber::LEVEL_INFO, 'domain5', 'info');
        mongoc_log(LogSubscriber::LEVEL_DEBUG, 'domain6', 'debug');

        $expectedLogs = [
            [LogLevel::ERROR, 'error', ['domain' => 'domain1']],
            [LogLevel::ERROR, 'critical', ['domain' => 'domain2']],
            [LogLevel::WARNING, 'warning', ['domain' => 'domain3']],
            [LogLevel::NOTICE, 'message', ['domain' => 'domain4']],
            [LogLevel::INFO, 'info', ['domain' => 'domain5']],
            [LogLevel::DEBUG, 'debug', ['domain' => 'domain6']],
        ];

        $this->assertSame($expectedLogs, $this->logger->logs);
    }

    /**
     * @testWith [-1]
     *           [9]
     */
    public function testWriteLogWithInvalidLevel(int $level): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(sprintf('Expected level to be >= 0 and <= 8, %d given for domain "domain" and message: message', $level));

        PsrLogAdapter::writeLog($level, 'domain', 'message');
    }

    public function testWriteLog(): void
    {
        PsrLogAdapter::writeLog(PsrLogAdapter::EMERGENCY, 'domain1', 'emergency');
        PsrLogAdapter::writeLog(PsrLogAdapter::ALERT, 'domain2', 'alert');
        PsrLogAdapter::writeLog(PsrLogAdapter::CRITICAL, 'domain3', 'critical');
        PsrLogAdapter::writeLog(PsrLogAdapter::ERROR, 'domain4', 'error');
        PsrLogAdapter::writeLog(PsrLogAdapter::WARN, 'domain5', 'warn');
        PsrLogAdapter::writeLog(PsrLogAdapter::NOTICE, 'domain6', 'notice');
        PsrLogAdapter::writeLog(PsrLogAdapter::INFO, 'domain7', 'info');
        PsrLogAdapter::writeLog(PsrLogAdapter::DEBUG, 'domain8', 'debug');
        PsrLogAdapter::writeLog(PsrLogAdapter::TRACE, 'domain9', 'trace');

        $expectedLogs = [
            [LogLevel::EMERGENCY, 'emergency', ['domain' => 'domain1']],
            [LogLevel::ALERT, 'alert', ['domain' => 'domain2']],
            [LogLevel::CRITICAL, 'critical', ['domain' => 'domain3']],
            [LogLevel::ERROR, 'error', ['domain' => 'domain4']],
            [LogLevel::WARNING, 'warn', ['domain' => 'domain5']],
            [LogLevel::NOTICE, 'notice', ['domain' => 'domain6']],
            [LogLevel::INFO, 'info', ['domain' => 'domain7']],
            [LogLevel::DEBUG, 'debug', ['domain' => 'domain8']],
            [LogLevel::DEBUG, 'trace', ['domain' => 'domain9']],
        ];

        $this->assertSame($expectedLogs, $this->logger->logs);
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
                $this->logs[] = func_get_args();
            }
        };
    }
}
