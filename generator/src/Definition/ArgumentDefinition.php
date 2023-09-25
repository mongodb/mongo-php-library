<?php

namespace MongoDB\CodeGenerator\Definition;

readonly class ArgumentDefinition
{
    public string $name;
    public string $type;
    public bool $isOptional;
    public bool $isVariadic;

    public function __construct(array $config)
    {
        $this->name = $config['name'];
        $this->type = $config['type'];
        $this->isOptional = $config['isOptional'] ?? false;
        $this->isVariadic = $config['isVariadic'] ?? false;
    }
}