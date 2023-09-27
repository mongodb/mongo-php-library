<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;

use function is_array;
use function is_string;

final readonly class ArgumentDefinition
{
    public function __construct(
        public string $name,
        /** @var string|list<string> */
        public string|array $type,
        public bool $isOptional = false,
        public bool $isVariadic = false,
        public int $variadicMin = 1,
    ) {
        if (is_array($type)) {
            foreach ($type as $t) {
                if (! is_string($t)) {
                    throw new InvalidArgumentException('Argument type must be a string or list of strings');
                }
            }
        }
    }
}
