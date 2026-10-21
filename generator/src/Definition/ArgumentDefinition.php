<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;

use function array_is_list;
use function assert;
use function get_debug_type;
use function is_array;
use function is_object;
use function is_string;
use function ltrim;
use function sprintf;
use function version_compare;

final class ArgumentDefinition
{
    public string $propertyName;
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
        public bool $mergeObject = false,
        public string|null $minVersion = null,
        public int|null $minItems = null,
        public int|null $maxItems = null,
        public int|null $valueMin = null,
        public int|null $valueMax = null,
        mixed ...$ignoredOtherArgs,
    ) {
        assert($this->optional === false || $this->default === null, 'Optional arguments cannot have a default value');
        if (is_array($this->type)) {
            assert(array_is_list($type), 'Type must be a list or a single string');
            foreach ($this->type as &$t) {
                if (is_object($t)) {
                    $t = $t->name ?? throw new InvalidArgumentException('Type array must have a "name" key');
                }

                assert(is_string($t), sprintf('Type must be a list of strings. Got %s', get_debug_type($t)));
            }
        }

        if ($valueMin !== null && $valueMax !== null) {
            assert($valueMin <= $valueMax, 'Min value must be less than or equal to max value');
        }

        $this->propertyName = ltrim($this->name, '$');

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

        if ($this->minVersion && version_compare($this->minVersion, '4.4', '>=')) {
            $this->description .= sprintf("\nNew in MongoDB %s\n", $this->minVersion);
        }
    }
}
