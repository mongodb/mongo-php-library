<?php

namespace MongoDB\CodeGenerator\Definition;

use Generator;
use Symfony\Component\Yaml\Yaml;

use function array_key_exists;

class YamlReader
{
    private array $definitions = [];

    public function read(string $filename): array
    {
        if (array_key_exists($filename, $this->definitions)) {
            return $this->definitions[$filename];
        }

        $config = Yaml::parseFile($filename);

        $definitions = [];
        foreach ($config as $operator) {
            $definitions[] = new OperatorDefinition($operator);
        }

        return $definitions;
    }
}
