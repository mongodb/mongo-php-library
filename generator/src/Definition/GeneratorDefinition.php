<?php

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;
use MongoDB\CodeGenerator\AbstractGenerator;

use function array_key_exists;
use function is_subclass_of;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

class GeneratorDefinition
{
    public readonly string $configFile;
    /** @var class-string<AbstractGenerator> */
    public readonly string $generatorClass;
    public readonly string $namespace;
    public readonly string $classNameSuffix;
    public readonly string $filePath;
    public readonly array $interfaces;
    public readonly ?string $parentClass;

    public function __construct(array $config)
    {
        // @todo check required keys and unexpected keys
        if (! array_key_exists('generatorClass', $config)) {
            throw new InvalidArgumentException('Missing required key "generatorClass"');
        }

        if (! is_subclass_of($config['generatorClass'], AbstractGenerator::class)) {
            throw new InvalidArgumentException(sprintf('Generator class "%s" must extend "%s".', $config['generatorClass'], AbstractGenerator::class));
        }

        if (! array_key_exists('filePath', $config)) {
            throw new InvalidArgumentException('Missing required key "filePath"');
        }

        if (! str_ends_with($config['filePath'], '/')) {
            throw new InvalidArgumentException(sprintf('File path must end with "/". Got "%s".', $config['filePath']));
        }

        if (! array_key_exists('namespace', $config)) {
            throw new InvalidArgumentException('Missing required key "namespace"');
        }

        if (! str_starts_with($config['namespace'], 'MongoDB\\')) {
            throw new InvalidArgumentException(sprintf('Namespace must start with "MongoDB\\". Got "%s".', $config['namespace']));
        }

        if (str_ends_with($config['namespace'], '\\')) {
            throw new InvalidArgumentException(sprintf('Namespace must not end with "\\". Got "%s".', $config['namespace']));
        }

        $this->configFile = $config['configFile'];
        $this->generatorClass = $config['generatorClass'];
        $this->namespace = $config['namespace'];
        $this->classNameSuffix = $config['classNameSuffix'] ?? '';
        $this->filePath = $config['filePath'];
        $this->interfaces = $config['interfaces'] ?? [];
        $this->parentClass = $config['parentClass'] ?? null;
    }
}
