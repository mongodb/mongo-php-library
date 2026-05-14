<?php

/**
 * This example demonstrates the full lifecycle of an Atlas Stream Processing
 * (ASP) stream processor using MongoDB\StreamProcessingClient. It creates,
 * starts, samples, stops, and drops a processor.
 *
 * Requirements:
 *
 *   - An Atlas Stream Processing workspace with a hostname matching the
 *     pattern atlas-stream-<workspaceId>-<suffix>.<region>.a.query.mongodb.net
 *   - A user with the `atlasAdmin` role
 *   - Two connections registered in the workspace:
 *       - `sample_stream_solar`  (built-in sample source)
 *       - `__testLog`            (built-in test sink)
 *
 * Use the MONGODB_STREAM_PROCESSING_URI environment variable to specify the
 * workspace connection string (including username/password). Example:
 *
 *   MONGODB_STREAM_PROCESSING_URI='mongodb://user:pass@atlas-stream-….a.query.mongodb.net/' \
 *       php examples/atlas_stream_processing.php
 */

declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Driver\Exception\Exception as DriverException;
use MongoDB\StreamProcessingClient;
use RuntimeException;
use Throwable;

use function count;
use function getenv;
use function sleep;
use function time;
use function uniqid;
use function var_export;

require __DIR__ . '/../vendor/autoload.php';

$uri = getenv('MONGODB_STREAM_PROCESSING_URI');
if (! $uri) {
    echo "This example requires an Atlas Stream Processing workspace endpoint.\n";
    echo "Set MONGODB_STREAM_PROCESSING_URI to the workspace connection string.\n";
    exit(1);
}

if (! StreamProcessingClient::isWorkspaceUri($uri)) {
    echo "MONGODB_STREAM_PROCESSING_URI does not look like a workspace endpoint.\n";
    echo "Expected hostname pattern: atlas-stream-*.<region>.a.query.mongodb.net (or mongodb-stage.net for Atlas staging)\n";
    exit(1);
}

$client = new StreamProcessingClient($uri);
$processors = $client->streamProcessors();
$name = 'phplib_demo_' . uniqid();

$pipeline = [
    ['$source' => ['connectionName' => 'sample_stream_solar']],
    ['$emit' => ['connectionName' => '__testLog', 'topic' => 'phplib-demo']],
];

echo 'Workspace: ', $uri, "\n";
echo 'Processor: ', $name, "\n\n";

try {
    /* --- create --- */
    echo '[1/6] create(' . $name . ")\n";
    $processors->create($name, $pipeline);
    $processor = $processors->get($name);

    $info = $processors->getInfo($name);
    echo '      state=', $info->getState(), "\n\n";

    /* --- start --- */
    echo "[2/6] start()\n";
    $processor->start();

    // Wait for the processor to reach STARTED before requesting stats.
    $deadline = time() + 30;
    $state = $processors->getInfo($name)->getState();
    while ($state !== 'STARTED' && time() < $deadline) {
        sleep(1);
        $state = $processors->getInfo($name)->getState();
    }

    echo '      state=', $state, "\n\n";
    if ($state !== 'STARTED') {
        throw new RuntimeException('Processor failed to reach STARTED within 30s; got ' . $state);
    }

    /* --- stats --- */
    echo "[3/6] stats()\n";
    $stats = $processor->stats();
    echo '      ', var_export($stats['stats'] ?? $stats, true), "\n\n";

    /* --- sample --- */
    echo "[4/6] getStreamProcessorSamples()\n";
    $samples = $processor->getStreamProcessorSamples(['limit' => 5]);
    echo '      open  cursorId=', $samples->getCursorId(), ' docs=', count($samples->getDocuments()), "\n";

    if (! $samples->isExhausted()) {
        // Give the stream a moment to produce something.
        sleep(2);
        $samples = $processor->getStreamProcessorSamples([
            'cursorId' => $samples->getCursorId(),
            'batchSize' => 5,
        ]);
        echo '      batch cursorId=', $samples->getCursorId(), ' docs=', count($samples->getDocuments()), "\n";
        foreach ($samples->getDocuments() as $i => $doc) {
            echo '          [', $i, '] ', var_export($doc, true), "\n";
        }
    }

    echo "\n";

    /* --- stop --- */
    echo "[5/6] stop()\n";
    $processor->stop();
    echo '      state=', $processors->getInfo($name)->getState(), "\n\n";

    /* --- drop --- */
    echo "[6/6] drop()\n";
    $processor->drop();
    echo "      dropped\n\n";

    echo "OK.\n";
    exit(0);
} catch (Throwable $e) {
    echo "\nFAILED: ", $e::class, ': ', $e->getMessage(), "\n";

    // Best-effort cleanup so we don't leave processors behind.
    try {
        $processors->get($name)->drop();
        echo '(cleaned up processor ' . $name . ")\n";
    } catch (DriverException) {
        // Processor may not exist, already be dropped, or be in a non-droppable state.
    }

    exit(1);
}
