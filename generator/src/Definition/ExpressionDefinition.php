<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

final readonly class ExpressionDefinition
{
    public function __construct(
        public string $name,
        /** @var list<string|class-string> */
        public array $types,
        public bool $class = false,
        /** @var list<class-string> */
        public array $implements = [],
    ) {
    }
}
