<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;

use function assert;
use function get_class;
use function getenv;
use function is_object;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function printf;

require __DIR__ . '/../vendor/autoload.php';

function toJSON(object $document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

class CommandLogger implements CommandSubscriber
{
    public function commandStarted(CommandStartedEvent $event): void
    {
        printf("%s command started\n", $event->getCommandName());

        printf("command: %s\n", toJson($event->getCommand()));
        printf("\n");
    }

    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        printf("%s command succeeded\n", $event->getCommandName());
        printf("reply: %s\n", toJson($event->getReply()));
        printf("\n");
    }

    public function commandFailed(CommandFailedEvent $event): void
    {
        printf("%s command failed\n", $event->getCommandName());
        printf("reply: %s\n", toJson($event->getReply()));

        $exception = $event->getError();
        printf("exception: %s\n", get_class($exception));
        printf("exception.code: %d\n", $exception->getCode());
        printf("exception.message: %s\n", $exception->getMessage());
        printf("\n");
    }
}

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$client->getManager()->addSubscriber(new CommandLogger());

$collection = $client->test->command_logger;
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
