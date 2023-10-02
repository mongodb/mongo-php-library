<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function assert;

final readonly class ExpressionDefinition
{
    public function __construct(
        public string $name,
        /** @var list<string|class-string> */
        public array $types,
        public bool $scalar = false,
        public bool $class = false,
        public ?string $extends = null,
        /** @var list<class-string> */
        public array $implements = [],
    ) {
        assert($class || ! $extends, 'Cannot specify "extends" when "class" is not true');
    }
}
