<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;
use MongoDB\CodeGenerator\OperatorGenerator;

use function is_subclass_of;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

final readonly class GeneratorDefinition
{
    public function __construct(
        public readonly string $configFile,
        /**
         * @var class-string<OperatorGenerator>
         * @psalm-assert class-string<OperatorGenerator>
         */
        public readonly string $generatorClass,
        public readonly string $namespace,
        public readonly string $filePath,
        public readonly string $classNameSuffix = '',
        public readonly array $interfaces = [],
        public readonly ?string $parentClass = null,
    ) {
        if (str_ends_with($this->filePath, '/')) {
            throw new InvalidArgumentException(sprintf('File path must not end with "/". Got "%s".', $this->filePath));
        }

        if (! str_starts_with($this->namespace, 'MongoDB\\')) {
            throw new InvalidArgumentException(sprintf('Namespace must start with "MongoDB\\". Got "%s".', $this->namespace));
        }

        if (str_ends_with($this->namespace, '\\')) {
            throw new InvalidArgumentException(sprintf('Namespace must not end with "\\". Got "%s".', $this->namespace));
        }

        if (! is_subclass_of($this->generatorClass, OperatorGenerator::class)) {
            throw new InvalidArgumentException(sprintf('Generator class "%s" must extend "%s".', $this->generatorClass, OperatorGenerator::class));
        }
    }
}
