<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use function assert;
use function is_array;

final class YamlReader
{
    /** @return list<OperatorDefinition> */
    public function read(string $dirname): array
    {
        $finder = new Finder();
        $finder->files()->in($dirname)->name('*.yaml')->sortByName();

        $definitions = [];
        foreach ($finder as $file) {
            $operator = Yaml::parseFile($file->getPathname());
            assert(is_array($operator));
            $definitions[] = new OperatorDefinition(...$operator);
        }

        return $definitions;
    }
}
