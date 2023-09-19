<?php

namespace MongoDB\Benchmark\Extension;

use MongoDB\Benchmark\Utils;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use PhpBench\Environment\Information;
use PhpBench\Environment\ProviderInterface;

use function array_merge;
use function implode;
use function parse_url;
use function str_replace;

use const PHP_URL_PASS;

class EnvironmentProvider implements ProviderInterface
{
    public function isApplicable(): bool
    {
        return true;
    }

    public function getInformation(): Information
    {
        $manager = Utils::getClient()->getManager();

        return new Information('mongodb', array_merge(
            $this->getUri(),
            $this->getServerInfo($manager),
            $this->getBuildInfo($manager),
        ));
    }

    private function getUri(): array
    {
        $uri = Utils::getUri();
        // Obfuscate the password in the URI
        $uri = str_replace(':' . parse_url($uri, PHP_URL_PASS) . '@', ':***@', $uri);

        return ['uri' => $uri];
    }

    private function getServerInfo(Manager $manager): array
    {
        $topology = match ($manager->selectServer()->getType()) {
            Server::TYPE_STANDALONE => 'standalone',
            Server::TYPE_LOAD_BALANCER => 'load-balanced',
            Server::TYPE_RS_PRIMARY => 'replica-set',
            Server::TYPE_MONGOS => 'sharded',
            default => 'unknown',
        };

        return ['topology' => $topology];
    }

    private function getBuildInfo(Manager $manager): array
    {
        $buildInfo = $manager->executeCommand(
            Utils::getDatabaseName(),
            new Command(['buildInfo' => 1]),
            new ReadPreference(ReadPreference::PRIMARY),
        )->toArray()[0];

        return [
            'version' => $buildInfo->version ?? 'unknown server version',
            'modules' => implode(', ', $buildInfo->modules ?? []),
        ];
    }
}
