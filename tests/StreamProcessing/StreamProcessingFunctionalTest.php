<?php

namespace MongoDB\Tests\StreamProcessing;

use MongoDB\Driver\Exception\Exception;
use MongoDB\Model\StreamProcessorInfo;
use MongoDB\StreamProcessingClient;
use MongoDB\StreamProcessor;
use MongoDB\Tests\TestCase;
use Throwable;

use function getenv;
use function uniqid;

/**
 * Functional lifecycle smoke test for Atlas Stream Processing.
 *
 * Skipped unless MONGODB_STREAM_PROCESSING_URI is set to a workspace endpoint
 * (atlas-stream-*.a.query.mongodb.net) with valid credentials.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class StreamProcessingFunctionalTest extends TestCase
{
    private StreamProcessingClient $client;

    private string $processorName;

    public function setUp(): void
    {
        parent::setUp();

        $uri = getenv('MONGODB_STREAM_PROCESSING_URI');
        if ($uri === false || $uri === '') {
            $this->markTestSkipped('MONGODB_STREAM_PROCESSING_URI is not configured');
        }

        if (! StreamProcessingClient::isWorkspaceUri($uri)) {
            $this->markTestSkipped('MONGODB_STREAM_PROCESSING_URI is not a workspace endpoint');
        }

        $this->client = new StreamProcessingClient($uri);
        $this->processorName = 'phplib_test_' . uniqid();
    }

    public function tearDown(): void
    {
        try {
            $this->client->streamProcessors()->get($this->processorName)->drop();
        } catch (Throwable) {
            // Best-effort cleanup; processor may not exist or already be dropped.
        }

        parent::tearDown();
    }

    public function testLifecycle(): void
    {
        $sps = $this->client->streamProcessors();

        $pipeline = [
            ['$source' => ['connectionName' => 'sample_stream_solar']],
            ['$emit' => ['connectionName' => '__testLog', 'topic' => 'phplib-output']],
        ];

        $sps->create($this->processorName, $pipeline);

        $info = $sps->getInfo($this->processorName);
        $this->assertInstanceOf(StreamProcessorInfo::class, $info);
        $this->assertSame($this->processorName, $info->getName());
        $this->assertContains($info->getState(), ['CREATED', 'VALIDATING', 'CREATING']);

        $processor = $sps->get($this->processorName);
        $this->assertInstanceOf(StreamProcessor::class, $processor);
        $processor->start();

        // Allow processor to reach STARTED before requesting stats.
        $this->assertNotEmpty($processor->stats());

        // First sample call opens the cursor (no documents yet).
        $samples = $processor->getStreamProcessorSamples(['limit' => 5]);
        $this->assertGreaterThan(0, $samples->getCursorId());
        $this->assertSame([], $samples->getDocuments());

        // Subsequent call(s) fetch batches via getMore.
        $batch = $processor->getStreamProcessorSamples([
            'cursorId' => $samples->getCursorId(),
            'batchSize' => 5,
        ]);
        // We can't assert non-empty (stream may have nothing yet) but exhaustion
        // detection MUST work either way.
        if ($batch->isExhausted()) {
            $this->assertSame(0, $batch->getCursorId());
        } else {
            $this->assertGreaterThan(0, $batch->getCursorId());
        }

        $processor->stop();
        $processor->drop();
    }

    public function testGetInfoOnMissingProcessorThrows(): void
    {
        $this->expectException(Exception::class);
        $this->client->streamProcessors()->getInfo('does_not_exist_' . uniqid());
    }
}
