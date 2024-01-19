<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use function get_object_vars;

final class YamlReader
{
    /** @return list<OperatorDefinition> */
    public function read(string $dirname): array
    {
        $finder = new Finder();
        $finder->files()->in($dirname)->name('*.yaml')->sortByName();

        $definitions = [];
        foreach ($finder as $file) {
            $operator = Yaml::parseFile(
                $file->getPathname(),
                Yaml::PARSE_OBJECT | Yaml::PARSE_OBJECT_FOR_MAP | Yaml::PARSE_CUSTOM_TAGS,
            );
            $definitions[] = new OperatorDefinition(...get_object_vars($operator));
        }

        return $definitions;
    }
}
