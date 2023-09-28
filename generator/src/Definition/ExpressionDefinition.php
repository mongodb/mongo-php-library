<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;

final readonly class ExpressionDefinition
{
    public function __construct(
        public string $name,
        /** @var list<string|class-string> */
        public array $types,
        public bool $class = false,
        public ?string $extends = null,
        /** @var list<class-string> */
        public array $implements = [],
    ) {
        if ($extends && ! $class) {
            throw new InvalidArgumentException('Cannot specify "extends" when "class" is not true');
        }
    }
}
