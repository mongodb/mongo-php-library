<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function assert;
use function is_array;
use function is_string;

final readonly class ArgumentDefinition
{
    public function __construct(
        public string $name,
        /** @psalm-assert string|list<string> $type */
        public string|array $type,
        public bool $isOptional = false,
        public bool $isVariadic = false,
        public int $variadicMin = 1,
    ) {
        if (is_array($type)) {
            foreach ($type as $t) {
                assert(is_string($t));
            }
        }
    }
}
