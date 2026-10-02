<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use function array_walk_recursive;
use function get_object_vars;
use function is_array;
use function is_object;
use function is_string;
use function rtrim;

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
            $operator = $this->stripTrailingNewlines($operator);
            $definitions[] = new OperatorDefinition(...get_object_vars($operator));
        }

        return $definitions;
    }

    /**
     * Strip trailing newlines from strings recursively.
     *
     * Since symfony/yaml 7.4.20, literal block scalars keep their trailing
     * newline as required by the YAML specification. Earlier versions dropped
     * it, which the generated output relies on. Stripping the newlines keeps
     * the generated output independent of the symfony/yaml version.
     */
    private function stripTrailingNewlines(mixed $data): mixed
    {
        if (is_string($data)) {
            return rtrim($data, "\n");
        }

        if (is_object($data)) {
            foreach (get_object_vars($data) as $key => $value) {
                $data->{$key} = $this->stripTrailingNewlines($value);
            }

            return $data;
        }

        if (is_array($data)) {
            array_walk_recursive($data, function (&$value): void {
                $value = $this->stripTrailingNewlines($value);
            });
        }

        return $data;
    }
}
