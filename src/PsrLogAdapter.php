<?php
/*
 * Copyright 2023-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use MongoDB\Driver\Monitoring\LogSubscriber;
use MongoDB\Exception\UnexpectedValueException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SplObjectStorage;

use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;
use function sprintf;

/**
 * Integrates libmongoc/PHPC logging with one or more PSR-3 loggers.
 *
 * This class is internal and should not be utilized by applications. Logging
 * should be configured via the add_logger() and remove_logger() functions.
 *
 * @internal
 */
final class PsrLogAdapter implements LogSubscriber
{
    public const EMERGENCY = 0;
    public const ALERT = 1;
    public const CRITICAL = 2;
    public const ERROR = 3;
    public const WARN = 4;
    public const NOTICE = 5;
    public const INFO = 6;
    public const DEBUG = 7;
    public const TRACE = 8;

    private static ?self $instance = null;

    /** @psalm-var SplObjectStorage<LoggerInterface, null> */
    private SplObjectStorage $loggers;

    private const SPEC_TO_PSR = [
        self::EMERGENCY => LogLevel::EMERGENCY,
        self::ALERT => LogLevel::ALERT,
        self::CRITICAL => LogLevel::CRITICAL,
        self::ERROR => LogLevel::ERROR,
        self::WARN => LogLevel::WARNING,
        self::NOTICE => LogLevel::NOTICE,
        self::INFO => LogLevel::INFO,
        self::DEBUG => LogLevel::DEBUG,
        // PSR does not define a "trace" level, so map it to "debug"
        self::TRACE => LogLevel::DEBUG,
    ];

    private const MONGOC_TO_PSR = [
        LogSubscriber::LEVEL_ERROR => LogLevel::ERROR,
        /* libmongoc considers "critical" less severe than "error" so map it to
         * "error" in the PSR logger. */
        LogSubscriber::LEVEL_CRITICAL => LogLevel::ERROR,
        LogSubscriber::LEVEL_WARNING => LogLevel::WARNING,
        LogSubscriber::LEVEL_MESSAGE => LogLevel::NOTICE,
        LogSubscriber::LEVEL_INFO => LogLevel::INFO,
        LogSubscriber::LEVEL_DEBUG => LogLevel::DEBUG,
    ];

    public static function addLogger(LoggerInterface $logger): void
    {
        $instance = self::getInstance();

        $instance->loggers->attach($logger);

        addSubscriber($instance);
    }

    /**
     * Forwards a log message from libmongoc/PHPC to all registered PSR loggers.
     *
     * @see LogSubscriber::log()
     */
    public function log(int $mongocLevel, string $domain, string $message): void
    {
        if (! isset(self::MONGOC_TO_PSR[$mongocLevel])) {
            throw new UnexpectedValueException(sprintf(
                'Expected level to be >= %d and <= %d, %d given for domain "%s" and message: %s',
                LogSubscriber::LEVEL_ERROR,
                LogSubscriber::LEVEL_DEBUG,
                $mongocLevel,
                $domain,
                $message,
            ));
        }

        $instance = self::getInstance();
        $psrLevel = self::MONGOC_TO_PSR[$mongocLevel];
        $context = ['domain' => $domain];

        foreach ($instance->loggers as $logger) {
            $logger->log($psrLevel, $message, $context);
        }
    }

    public static function removeLogger(LoggerInterface $logger): void
    {
        $instance = self::getInstance();
        $instance->loggers->detach($logger);

        if ($instance->loggers->count() === 0) {
            removeSubscriber($instance);
        }
    }

    /**
     * Writes a log message to all registered PSR loggers.
     *
     * This function is intended for internal use within the library.
     */
    public static function writeLog(int $specLevel, string $domain, string $message): void
    {
        if (! isset(self::SPEC_TO_PSR[$specLevel])) {
            throw new UnexpectedValueException(sprintf(
                'Expected level to be >= %d and <= %d, %d given for domain "%s" and message: %s',
                self::EMERGENCY,
                self::TRACE,
                $specLevel,
                $domain,
                $message,
            ));
        }

        $instance = self::getInstance();
        $psrLevel = self::SPEC_TO_PSR[$specLevel];
        $context = ['domain' => $domain];

        foreach ($instance->loggers as $logger) {
            $logger->log($psrLevel, $message, $context);
        }
    }

    private function __construct()
    {
        $this->loggers = new SplObjectStorage();
    }

    private static function getInstance(): self
    {
        return self::$instance ??= new self();
    }
}
