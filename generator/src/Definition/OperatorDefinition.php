<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;

use function array_map;
use function count;
use function in_array;
use function sprintf;

final readonly class OperatorDefinition
{
    /** @var list<ArgumentDefinition> */
    public array $arguments;

    public function __construct(
        public string $name,
        public ?string $encode = null,
        public ?string $type = null,
        public bool $usesNamedArgs = false,
        array $args = [],
    ) {
        if ($encode === null && count($args) !== 1) {
            throw new InvalidArgumentException(sprintf('Operator "%s" have %s arguments, the "encode" parameter must be specified.', $this->name, count($args)));
        }

        if (! in_array($this->encode, [null, 'array', 'object'], true)) {
            throw new InvalidArgumentException(sprintf('Operator "%s" expect "encode" value to be "array" or "object". Got "%s".', $this->name, $this->encode));
        }

        $this->arguments = array_map(
            fn ($arg): ArgumentDefinition => new ArgumentDefinition(...$arg),
            $args,
        );
    }
}
