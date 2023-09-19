<?php

namespace MongoDB\Benchmark\Extension;

use PhpBench\DependencyInjection\Container;
use PhpBench\DependencyInjection\ExtensionInterface;
use PhpBench\Extension\RunnerExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MongoDBExtension implements ExtensionInterface
{
    public function load(Container $container): void
    {
        $container->register(
            EnvironmentProvider::class,
            fn (Container $container) => new EnvironmentProvider(),
            [RunnerExtension::TAG_ENV_PROVIDER => ['name' => 'mongodb']],
        );
    }

    public function configure(OptionsResolver $resolver): void
    {
        // No config
    }
}
