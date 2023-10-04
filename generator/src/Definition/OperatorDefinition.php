<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function array_map;
use function assert;
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
        array $args = [],
    ) {
        assert($encode || count($args) === 1, sprintf('Operator "%s" has %d arguments. The "encode" parameter must be specified.', $name, count($args)));
        assert(in_array($encode, [null, 'array', 'object'], true), sprintf('Operator "%s" expect "encode" value to be "array" or "object". Got "%s".', $name, $encode));

        $this->arguments = array_map(
            fn ($arg): ArgumentDefinition => new ArgumentDefinition(...$arg),
            $args,
        );
    }
}
