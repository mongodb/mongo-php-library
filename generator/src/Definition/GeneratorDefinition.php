<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use MongoDB\CodeGenerator\OperatorGenerator;

use function assert;
use function is_string;
use function is_subclass_of;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

final readonly class GeneratorDefinition
{
    public function __construct(
        public string $configFile,
        /** @psalm-assert list<class-string<OperatorGenerator>> */
        public array $generators,
        public string $namespace,
        public string $classNameSuffix = '',
        public array $interfaces = [],
        public ?string $parentClass = null,
    ) {
        assert(str_starts_with($this->namespace, 'MongoDB\\'), sprintf('Namespace must start with "MongoDB\\". Got "%s"', $this->namespace));
        assert(! str_ends_with($this->namespace, '\\'), sprintf('Namespace must not end with "\\". Got "%s"', $this->namespace));
        foreach ($this->generators as $class) {
            assert(is_string($class) && is_subclass_of($class, OperatorGenerator::class), sprintf('Generator class "%s" must extend "%s"', $class, OperatorGenerator::class));
        }
    }
}
