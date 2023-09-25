<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use InvalidArgumentException;
use MongoDB\Aggregation\Expression;
use MongoDB\CodeGenerator\Definition\ArgumentDefinition;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\PsrPrinter;

use function array_map;
use function dirname;
use function file_put_contents;
use function implode;
use function in_array;
use function is_dir;
use function mkdir;
use function sort;
use function str_starts_with;
use function ucfirst;

/** @internal */
abstract class AbstractGenerator
{
    /** @var array<string, list<string|class-string>> */
    protected array $typeAliases = [
        'resolvesToExpression' => [Expression\ResolvesToExpression::class, 'array', 'object', 'string', 'int', 'float', 'bool', 'null'],
        'resolvesToArrayExpression' => [Expression\ResolvesToArrayExpression::class, 'array', 'object', 'string'],
        'resolvesToBoolExpression' => [Expression\ResolvesToBoolExpression::class, 'array', 'object', 'string', 'bool'],
        'resolvesToMatchExpression' => ['array', 'object', Expression\ResolvesToMatchExpression::class],
        'resolvesToNumberExpression' => [Expression\ResolvesToBoolExpression::class, 'array', 'object', 'string', 'int', 'float'],
        'resolvesToQueryOperator' => ['array', 'object', Expression\ResolvesToQuery::class],
        'resolvesToSortSpecification' => ['array', 'object', Expression\ResolvesToSortSpecification::class],
    ];

    protected GeneratorDefinition $definition;
    protected Printer $printer;

    public function __construct(GeneratorDefinition $definition)
    {
        $this->validate($definition);

        $this->definition = $definition;
        $this->printer = new PsrPrinter();
    }

    /** @throws InvalidArgumentException when definition is invalid */
    protected function validate(GeneratorDefinition $definition): void
    {
    }

    public function createClassesForObjects(array $objects): void
    {
        foreach ($objects as $object) {
            $this->createFileForClass(
                $this->definition->filePath,
                $this->createClassForObject($object),
            );
        }
    }

    abstract public function createClassForObject(object $object): ClassType;

    /** @return array{native:string,doc:string} */
    final protected function generateTypeString(ArgumentDefinition $arg): array
    {
        $type = $arg->type;
        $nativeTypes = $this->typeAliases[$type] ?? [$type];
        $docTypes = $nativeTypes;

        foreach ($nativeTypes as $key => $typeName) {
            // @todo replace with class_exists
            if (str_starts_with($typeName, 'MongoDB\\')) {
                $nativeTypes[$key] = $docTypes[$key] = '\\' . $typeName;

                // A union cannot contain both object and a class type, which is redundant and causes a PHP error
                if (in_array('object', $nativeTypes, true)) {
                    unset($nativeTypes[$key]);
                }
            }
        }

        sort($nativeTypes);
        sort($docTypes);

        if ($arg->isOptional) {
            $nativeTypes[] = 'null';
            $docTypes[] = 'null';
        }

        return [
            'native' => implode('|', $nativeTypes),
            'doc' => implode('|', $docTypes),
        ];
    }

    protected function getClassName(object $object): string
    {
        return ucfirst($object->name) . $this->definition->classNameSuffix;
    }

    protected function createFileForClass(string $dirname, ClassType $class): void
    {
        $fullName = $dirname . $class->getName() . '.php';

        $file = new PhpFile();
        $namespace = $file->addNamespace($this->definition->namespace);
        $namespace->add($class);

        $this->writeFileFromGenerator($fullName, $file);
    }

    protected function writeFileFromGenerator(string $filename, PhpFile $file): void
    {
        $dirname = dirname($filename);

        $file->setComment('THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!');

        if (! is_dir($dirname)) {
            mkdir($dirname, 0775, true);
        }

        file_put_contents($filename, $this->printer->printFile($file));
    }
}
