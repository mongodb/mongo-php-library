<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

readonly class ArgumentDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $isOptional = false,
        public bool $isVariadic = false,
    ) {
    }
}
