<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use MongoDB\CodeGenerator\OperatorGenerator;

use function array_is_list;
use function assert;
use function class_exists;
use function is_string;
use function is_subclass_of;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

final class GeneratorDefinition
{
    public function __construct(
        public string $configFiles,
        /** @var list<class-string<OperatorGenerator>> */
        public array $generators,
        public string $namespace,
        public string $classNameSuffix = '',
        public array $interfaces = [],
        public string|null $parentClass = null,
    ) {
        assert(str_starts_with($namespace, 'MongoDB\\'), sprintf('Namespace must start with "MongoDB\\". Got "%s"', $namespace));
        assert(! str_ends_with($namespace, '\\'), sprintf('Namespace must not end with "\\". Got "%s"', $namespace));

        assert(array_is_list($interfaces), 'Generators must be a list of class names');
        foreach ($interfaces as $interface) {
            assert(is_string($interface) && class_exists($interface), sprintf('Interface "%s" does not exist', $interface));
        }

        assert(array_is_list($generators), 'Generators must be a list of class names');
        foreach ($generators as $class) {
            assert(is_string($class) && is_subclass_of($class, OperatorGenerator::class), sprintf('Generator class "%s" must extend "%s"', $class, OperatorGenerator::class));
        }
    }
}
