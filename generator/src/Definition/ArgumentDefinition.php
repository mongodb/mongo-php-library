<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function assert;
use function is_array;
use function is_string;

final readonly class ArgumentDefinition
{
    public ?int $variadicMin;

    public function __construct(
        public string $name,
        /** @psalm-assert string|list<string> $type */
        public string|array $type,
        public bool $isOptional = false,
        public bool $isVariadic = false,
        ?int $variadicMin = null,
    ) {
        if (is_array($type)) {
            foreach ($type as $t) {
                assert(is_string($t), json_encode($type));
            }
        }

        if (! $isVariadic) {
            assert($variadicMin === null);
            $this->variadicMin = null;
        } elseif ($variadicMin === null) {
            $this->variadicMin = $isOptional ? 0 : 1;
        } else {
            $this->variadicMin = $variadicMin;
        }
    }
}
