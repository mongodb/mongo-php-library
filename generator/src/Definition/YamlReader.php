<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use function array_key_exists;
use function assert;
use function is_array;

final class YamlReader
{
    /** @var array<string, list<OperatorDefinition>> */
    private static array $definitions = [];

    /** @return list<OperatorDefinition> */
    public function read(string $dirname): array
    {
        if (array_key_exists($dirname, self::$definitions)) {
            return self::$definitions[$dirname];
        }

        $finder = new Finder();
        $finder->files()->in($dirname)->name('*.yaml');

        $definitions = [];
        foreach ($finder as $file) {
            $operator = Yaml::parseFile($file->getPathname());
            assert(is_array($operator));
            $definitions[] = new OperatorDefinition(...$operator);
        }

        return self::$definitions[$dirname] = $definitions;
    }
}
