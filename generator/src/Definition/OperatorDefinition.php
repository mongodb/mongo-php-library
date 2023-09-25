<?php

namespace MongoDB\CodeGenerator\Definition;

use function array_map;

readonly class OperatorDefinition
{
    public string $name;
    public bool $usesNamedArgs;

    /** @var list<ArgumentDefinition> */
    public array $arguments;

    public function __construct(array $config)
    {
        $this->name = $config['name'];
        $this->usesNamedArgs = $config['usesNamedArgs'] ?? false;
        $this->arguments = isset($config['args']) ? array_map(
            fn ($arg): ArgumentDefinition => new ArgumentDefinition($arg),
            $config['args'],
        ) : [];
    }
}
