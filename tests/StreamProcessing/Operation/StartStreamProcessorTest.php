<?php

namespace MongoDB\Tests\StreamProcessing\Operation;

use MongoDB\BSON\Timestamp;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\StreamProcessing\StartStreamProcessor;
use MongoDB\Tests\Operation\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class StartStreamProcessorTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StartStreamProcessor('proc', $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'workers' => self::getInvalidIntegerValues(),
            'clearCheckpoints' => self::getInvalidBooleanValues(),
            'startAtOperationTime' => [123, 3.14, 'foo', true, []],
            'tier' => self::getInvalidStringValues(),
            'enableAutoScaling' => self::getInvalidBooleanValues(),
        ]);
    }

    public function testStartAtOperationTimeAcceptsTimestamp(): void
    {
        $op = new StartStreamProcessor('proc', ['startAtOperationTime' => new Timestamp(0, 0)]);
        $this->assertInstanceOf(StartStreamProcessor::class, $op);
    }

    public function testFailoverRequiresRegion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"failover" option requires a "region" string');
        new StartStreamProcessor('proc', ['failover' => ['mode' => 'GRACEFUL']]);
    }

    public function testFailoverRejectsInvalidMode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid "failover.mode" value');
        new StartStreamProcessor('proc', ['failover' => ['region' => 'us-east-1', 'mode' => 'INVALID']]);
    }

    public function testFailoverAcceptsValidConfiguration(): void
    {
        $op = new StartStreamProcessor('proc', [
            'failover' => ['region' => 'us-east-1', 'mode' => 'FORCED', 'dryRun' => true],
        ]);
        $this->assertInstanceOf(StartStreamProcessor::class, $op);
    }
}
