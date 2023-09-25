<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use Symfony\Component\Yaml\Yaml;

use function array_key_exists;
use function assert;
use function is_array;

class YamlReader
{
    /** @var array<string, list<OperatorDefinition>> */
    private array $definitions = [];

    /** @return list<OperatorDefinition> */
    public function read(string $filename): array
    {
        if (array_key_exists($filename, $this->definitions)) {
            return $this->definitions[$filename];
        }

        $config = Yaml::parseFile($filename);
        assert(is_array($config));

        $definitions = [];
        foreach ($config as $operator) {
            assert(is_array($operator));
            $definitions[] = new OperatorDefinition(...$operator);
        }

        return $definitions;
    }
}
