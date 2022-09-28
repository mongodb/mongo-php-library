<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;

use function assert;
use function dirname;
use function fprintf;
use function get_class;
use function getenv;
use function is_object;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function printf;

use const STDERR;

require dirname(__FILE__) . '/../vendor/autoload.php';

function toJSON(object $document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
class CommandLogger implements CommandSubscriber
{
    public function commandStarted(CommandStartedEvent $event): void
    {
        fprintf(STDERR, "%s command started\n", $event->getCommandName());

        fprintf(STDERR, "command: %s\n", toJson($event->getCommand()));
        fprintf(STDERR, "\n");
    }

    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        fprintf(STDERR, "%s command succeeded\n", $event->getCommandName());
        fprintf(STDERR, "reply: %s\n", toJson($event->getReply()));
        fprintf(STDERR, "\n");
    }

    public function commandFailed(CommandFailedEvent $event): void
    {
        fprintf(STDERR, "%s command failed\n", $event->getCommandName());
        fprintf(STDERR, "reply: %s\n", toJson($event->getReply()));

        $exception = $event->getError();
        fprintf(STDERR, "exception: %s\n", get_class($exception));
        fprintf(STDERR, "exception.code: %d\n", $exception->getCode());
        fprintf(STDERR, "exception.message: %s\n", $exception->getMessage());
        fprintf(STDERR, "\n");
    }
}

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$client->getManager()->addSubscriber(new CommandLogger());

$collection = $client->test->coll;
$collection->drop();

$collection->insertMany([
    ['x' => 1],
    ['x' => 2],
    ['x' => 3],
]);

$collection->updateMany(
    ['x' => ['$gt' => 1]],
    ['$set' => ['y' => 1]]
);

$cursor = $collection->find([], ['batchSize' => 2]);

foreach ($cursor as $document) {
    assert(is_object($document));
    printf("%s\n", toJSON($document));
}
