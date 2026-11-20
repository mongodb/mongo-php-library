<?php

namespace MongoDB\Tests;

use MongoDB;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionMethod;
use RegexIterator;

use function array_filter;
use function array_map;
use function array_values;
use function in_array;
use function realpath;
use function str_contains;
use function str_replace;
use function strcasecmp;
use function strlen;
use function substr;
use function usort;

use const DIRECTORY_SEPARATOR;

/**
 * Pedantic tests that have nothing to do with functional correctness.
 */
class PedantryTest extends TestCase
{
    private const SKIPPED_CLASSES = [
        // Generated
        MongoDB\Builder\Stage\FluentFactoryTrait::class,
    ];

    #[DataProvider('provideProjectClassNames')]
    public function testMethodsAreOrderedAlphabeticallyByVisibility($className): void
    {
        $class = new ReflectionClass($className);
        $methods = $class->getMethods();

        $methods = array_values(array_filter(
            $methods,
            fn (ReflectionMethod $method) => $method->getDeclaringClass() == $class // Exclude inherited methods
                    && $method->getFileName() === $class->getFileName() // Exclude methods inherited from traits
                    && ! ($method->isConstructor() && ! $method->isPublic()), // Exclude non-public constructors
        ));

        $getSortValue = function (ReflectionMethod $method) {
            $prefix = $method->isPrivate() ? '2' : ($method->isProtected() ? '1' : '0');
            $prefix .= str_contains($method->getDocComment(), '@internal') ? '1' : '0';

            return $prefix . $method->getName();
        };

        $sortedMethods = $methods;
        usort(
            $sortedMethods,
            fn (ReflectionMethod $a, ReflectionMethod $b) => strcasecmp($getSortValue($a), $getSortValue($b)),
        );

        $methods = array_map(fn (ReflectionMethod $method) => $method->getName(), $methods);
        $sortedMethods = array_map(fn (ReflectionMethod $method) => $method->getName(), $sortedMethods);

        $this->assertEquals($sortedMethods, $methods);
    }

    public static function provideProjectClassNames()
    {
        $classNames = [];
        $srcDir = realpath(__DIR__ . '/../src/');

        $files = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir)), '/\.php$/i');

        foreach ($files as $file) {
            if ($file->getFilename() === 'functions.php') {
                continue;
            }

            /* autoload.php added downstream (e.g. Fedora) */
            if ($file->getFilename() === 'autoload.php') {
                continue;
            }

            $className = 'MongoDB\\' . str_replace(DIRECTORY_SEPARATOR, '\\', substr($file->getRealPath(), strlen($srcDir) + 1, -4));
            if (in_array($className, self::SKIPPED_CLASSES)) {
                continue;
            }

            $classNames[$className][] = $className;
        }

        return $classNames;
    }
}
