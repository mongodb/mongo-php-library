<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function array_is_list;
use function assert;
use function get_debug_type;
use function is_array;
use function is_string;
use function sprintf;

final class ArgumentDefinition
{
    public VariadicType|null $variadic;
    public int|null $variadicMin;

    public function __construct(
        public string $name,
        /** @var list<string> */
        public array $type,
        public string|null $description = null,
        public bool $optional = false,
        string|null $variadic = null,
        int|null $variadicMin = null,
        public mixed $default = null,
    ) {
        assert($this->optional === false || $this->default === null, 'Optional arguments cannot have a default value');
        if (is_array($type)) {
            assert(array_is_list($type), 'Type must be a list or a single string');
            foreach ($type as $t) {
                assert(is_string($t), sprintf('Type must be a list of strings. Got %s', get_debug_type($type)));
            }
        }

        if ($variadic) {
            $this->variadic = VariadicType::from($variadic);
            if ($variadicMin === null) {
                $this->variadicMin = $optional ? 0 : 1;
            } else {
                $this->variadicMin = $variadicMin;
            }
        } else {
            $this->variadic = null;
            $this->variadicMin = null;
        }
    }
}
