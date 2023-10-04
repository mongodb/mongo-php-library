<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\Builder\Aggregation\AccumulatorInterface;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Query\QueryInterface;
use MongoDB\Builder\Stage\StageInterface;
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
        /** @var array<class-string<ExpressionInterface>, ExpressionDefinition> */
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

    /** @return class-string<ExpressionInterface>|string */
    final protected function getExpressionTypeInterface(string $type): string
    {
        if ('expression' === $type || 'resolvesToAnything' === $type) {
            return ExpressionInterface::class;
        }

        if ('Stage' === $type) {
            return StageInterface::class;
        }

        if ('Accumulator' === $type) {
            return AccumulatorInterface::class;
        }

        if ('Query' === $type) {
            return QueryInterface::class;
        }

        // @todo handle generic types object<T> and array<T>
        $type = explode('<', $type, 2)[0];
        $type = explode('{', $type, 2)[0];

        // Scalar types
        if (array_key_exists($type, $this->expressions)) {
            return $type;
        }

        $interface = 'MongoDB\\Builder\\Expression\\' . ucfirst($type);
        assert(array_key_exists($interface, $this->expressions), sprintf('Invalid expression type "%s".', $type));

        return $interface;
    }

    /**
     * Expression types can contain class names, interface, native types or "list".
     * PHPDoc types are more precise than native types, so we use them systematically even if redundant.
     *
     * @return object{native:string,doc:string,use:list<class-string>,list:bool}
     */
    final protected function generateExpressionTypes(ArgumentDefinition $arg): object
    {
        $nativeTypes = [];
        foreach ($arg->type as $type) {
            $interface = $this->getExpressionTypeInterface($type);
            $types = $this->expressions[$interface]->types;

            // Add the interface to the allowed types if it is not a scalar
            if (! $this->expressions[$interface]->scalar) {
                $types = array_merge([$interface], $types);
            }

            $nativeTypes = array_merge($nativeTypes, $types);
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
                $docTypes[$key] = 'list<ExpressionInterface|mixed>';
                $use[] = '\\' . ExpressionInterface::class;
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
