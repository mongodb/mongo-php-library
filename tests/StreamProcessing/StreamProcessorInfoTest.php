<?php

namespace MongoDB\Tests\StreamProcessing;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\StreamProcessorInfo;
use MongoDB\Tests\TestCase;

class StreamProcessorInfoTest extends TestCase
{
    public function testGettersExposeKnownFields(): void
    {
        $info = new StreamProcessorInfo([
            'id' => 'proc-1',
            'name' => 'smokeTestProcessor',
            'state' => 'CREATED',
            'pipeline' => [['$source' => ['connectionName' => 'sample_stream_solar']]],
            'pipelineVersion' => 2,
            'tier' => 'SP2',
            'streamMetaFieldName' => '_stream_meta',
            'enableAutoScaling' => true,
            'failoverEnabled' => false,
            'activeRegion' => 'us-east-1',
            'workspaceDefaultRegion' => 'us-east-1',
            'lastStateChange' => new UTCDateTime(),
            'modifiedBy' => 'user-1',
            'hasStarted' => false,
            'errorMsg' => '',
            'errorRetryable' => false,
        ]);

        $this->assertSame('proc-1', $info->getId());
        $this->assertSame('smokeTestProcessor', $info->getName());
        $this->assertSame('CREATED', $info->getState());
        $this->assertSame(2, $info->getPipelineVersion());
        $this->assertSame('SP2', $info->getTier());
        $this->assertSame('_stream_meta', $info->getStreamMetaFieldName());
        $this->assertTrue($info->isAutoScalingEnabled());
        $this->assertFalse($info->isFailoverEnabled());
        $this->assertSame('us-east-1', $info->getActiveRegion());
        $this->assertSame('user-1', $info->getModifiedBy());
        $this->assertFalse($info->hasStarted());
        $this->assertSame('', $info->getErrorMsg());
        $this->assertFalse($info->isErrorRetryable());
        $this->assertNull($info->getErrorCode());
        $this->assertInstanceOf(UTCDateTime::class, $info->getLastStateChange());
    }

    public function testIdAndPipelineVersionAreOptional(): void
    {
        $info = new StreamProcessorInfo([
            'name' => 'p',
            'state' => 'CREATED',
            'pipeline' => [],
            'errorMsg' => '',
        ]);

        $this->assertNull($info->getId());
        $this->assertNull($info->getPipelineVersion());
        $this->assertSame('', $info->getErrorMsg());
    }

    public function testGettersReturnDefaultsWhenFieldsAreMissing(): void
    {
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED']);

        $this->assertNull($info->getActiveRegion());
        $this->assertNull($info->getDlq());
        $this->assertNull($info->getErrorCode());
        $this->assertSame('', $info->getErrorMsg());
        $this->assertNull($info->getId());
        $this->assertNull($info->getLastModifiedAt());
        $this->assertNull($info->getLastStateChange());
        $this->assertNull($info->getModifiedBy());
        $this->assertSame([], $info->getPipeline());
        $this->assertNull($info->getPipelineVersion());
        $this->assertNull($info->getStreamMetaFieldName());
        $this->assertNull($info->getTier());
        $this->assertNull($info->getWorkspaceDefaultRegion());
        $this->assertFalse($info->hasStarted());
        $this->assertFalse($info->isAutoScalingEnabled());
        $this->assertFalse($info->isErrorRetryable());
        $this->assertFalse($info->isFailoverEnabled());
    }

    public function testGetDlqReturnsRawValue(): void
    {
        $dlq = ['connectionName' => 'errors', 'db' => 'd', 'coll' => 'c'];
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED', 'dlq' => $dlq]);
        $this->assertSame($dlq, $info->getDlq());
    }

    public function testGetPipelineReturnsArray(): void
    {
        $pipeline = [['$source' => ['connectionName' => 's']], ['$emit' => ['connectionName' => 'e']]];
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED', 'pipeline' => $pipeline]);
        $this->assertSame($pipeline, $info->getPipeline());
    }

    public function testErrorFieldsAreExposedWhenSet(): void
    {
        $info = new StreamProcessorInfo([
            'name' => 'p',
            'state' => 'FAILED',
            'errorMsg' => 'something went wrong',
            'errorRetryable' => true,
            'errorCode' => 125,
        ]);

        $this->assertSame('something went wrong', $info->getErrorMsg());
        $this->assertTrue($info->isErrorRetryable());
        $this->assertSame(125, $info->getErrorCode());
    }

    public function testLastModifiedAtIsExposed(): void
    {
        $date = new UTCDateTime();
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED', 'lastModifiedAt' => $date]);
        $this->assertSame($date, $info->getLastModifiedAt());
    }

    public function testWorkspaceDefaultRegionIsExposed(): void
    {
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED', 'workspaceDefaultRegion' => 'us-west-2']);
        $this->assertSame('us-west-2', $info->getWorkspaceDefaultRegion());
    }

    public function testDebugInfoReturnsUnderlyingArray(): void
    {
        $data = ['name' => 'p', 'state' => 'CREATED'];
        $info = new StreamProcessorInfo($data);
        $this->assertSame($data, $info->__debugInfo());
    }

    public function testArrayAccessIsReadOnly(): void
    {
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED']);
        $this->assertSame('p', $info['name']);
        $this->assertSame('CREATED', $info['state']);
        $this->assertTrue(isset($info['state']));
        $this->assertFalse(isset($info['nope']));
    }

    public function testOffsetSetThrows(): void
    {
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED']);
        $this->expectException(BadMethodCallException::class);
        $info['name'] = 'q';
    }

    public function testOffsetUnsetThrows(): void
    {
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED']);
        $this->expectException(BadMethodCallException::class);
        unset($info['name']);
    }
}
