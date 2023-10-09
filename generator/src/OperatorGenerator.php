<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\Builder\Expression\ExpressionType;
use MongoDB\Builder\Optional;
use MongoDB\CodeGenerator\Definition\ArgumentDefinition;
use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use MongoDB\CodeGenerator\Definition\YamlReader;
use Nette\PhpGenerator\Type;

use function array_filter;
use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function class_exists;
use function explode;
use function in_array;
use function interface_exists;
use function ltrim;
use function sort;
use function sprintf;
use function ucfirst;

abstract class OperatorGenerator extends AbstractGenerator
{
    private YamlReader $yamlReader;

    final public function __construct(
        string $rootDir,
        /** @var array<class-string<ExpressionType>, ExpressionDefinition> */
        private array $expressions
    ) {
        parent::__construct($rootDir);

        $this->yamlReader = new YamlReader();
    }

    abstract public function generate(GeneratorDefinition $definition): void;

    final protected function getOperators(GeneratorDefinition $definition): array
    {
        // Remove unsupported operators
        return array_filter(
            $this->yamlReader->read($definition->configFiles),
            fn (OperatorDefinition $operator): bool => ! in_array($operator->name, ['$'], true),
        );
    }

    final protected function getOperatorClassName(GeneratorDefinition $definition, OperatorDefinition $operator): string
    {
        return ucfirst(ltrim($operator->name, '$')) . $definition->classNameSuffix;
    }

    final protected function getType(string $type): ExpressionDefinition
    {
        // @todo handle generic types object<T> and array<T>
        $type = explode('<', $type, 2)[0];
        $type = explode('{', $type, 2)[0];

        $type = match ($type) {
            'list' => 'array',
            default => $type,
        };

        assert(array_key_exists($type, $this->expressions), sprintf('Invalid expression type "%s".', $type));

        return $this->expressions[$type];
    }

    /**
     * Expression types can contain class names, interface, native types or "list".
     * PHPDoc types are more precise than native types, so we use them systematically even if redundant.
     *
     * @return object{native:string,doc:string,use:list<class-string>,list:bool}
     */
    final protected function getAcceptedTypes(ArgumentDefinition $arg): object
    {
        $nativeTypes = [];
        foreach ($arg->type as $type) {
            $type = $this->getType($type);
            $nativeTypes = array_merge($nativeTypes, $type->acceptedTypes);

            if (isset($type->returnType)) {
                $nativeTypes[] = $type->returnType;
            }
        }

        if ($arg->optional) {
            $use[] = '\\' . Optional::class;
            $nativeTypes[] = Optional::class;
        }

        $docTypes = $nativeTypes = array_unique($nativeTypes);
        $listCheck = false;
        $use = [];

        foreach ($nativeTypes as $key => $typeName) {
            // "list" is a special type of array that needs to be checked in the code
            if ($typeName === 'list') {
                $listCheck = true;
                $nativeTypes[$key] = 'array';
                // @todo allow to specify the type of the elements in the list
                $docTypes[$key] = 'array';
                continue;
            }

            // strings cannot be empty
            if ($typeName === 'string') {
                $docTypes[$key] = 'non-empty-string';
            }

            if (interface_exists($typeName) || class_exists($typeName)) {
                $use[] = $nativeTypes[$key] = '\\' . $typeName;
                $docTypes[$key] = $this->splitNamespaceAndClassName($typeName)[1];
                // A union cannot contain both object and a class type
                if (in_array('object', $nativeTypes, true)) {
                    unset($nativeTypes[$key]);
                }
            }
        }

        // mixed can only be used as a standalone type
        if (in_array('mixed', $nativeTypes, true)) {
            $nativeTypes = ['mixed'];
        }

        sort($nativeTypes);
        sort($docTypes);
        sort($use);

        return (object) [
            'native' => Type::union(...array_unique($nativeTypes)),
            'doc' => Type::union(...array_unique($docTypes)),
            'use' => array_unique($use),
            'list' => $listCheck,
        ];
    }
}
