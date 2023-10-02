<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function array_is_list;
use function assert;
use function get_debug_type;
use function is_array;
use function is_string;
use function sprintf;

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
            assert(array_is_list($type), 'Type must be a list or a single string');
            foreach ($type as $t) {
                assert(is_string($t), sprintf('Type must be a list of strings. Got %s', get_debug_type($type)));
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
