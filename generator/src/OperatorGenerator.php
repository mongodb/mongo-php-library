<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use InvalidArgumentException;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\CodeGenerator\Definition\ArgumentDefinition;
use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use MongoDB\CodeGenerator\Definition\YamlReader;
use Nette\PhpGenerator\Type;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function class_exists;
use function in_array;
use function interface_exists;
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

    /** @return list<OperatorDefinition> */
    final protected function getOperators(GeneratorDefinition $definition): array
    {
        return $this->yamlReader->read($definition->configFile);
    }

    final protected function getOperatorClassName(GeneratorDefinition $definition, OperatorDefinition $operator): string
    {
        return ucfirst($operator->name) . $definition->classNameSuffix;
    }

    /** @return class-string<ExpressionInterface> */
    final protected function getExpressionTypeInterface(string $type): string
    {
        if ('expression' === $type) {
            return ExpressionInterface::class;
        }

        $interface = 'MongoDB\\Builder\\Expression\\' . ucfirst($type);

        if (! array_key_exists($interface, $this->expressions)) {
            throw new InvalidArgumentException(sprintf('Invalid expression type "%s".', $type));
        }

        return $interface;
    }

    /**
     * Expression types can contain class names, interface, native types or "list"
     *
     * @return object{native:string,doc:string,use:list<class-string>,list:bool}
     */
    final protected function generateExpressionTypes(ArgumentDefinition $arg): object
    {
        $nativeTypes = [];
        foreach ((array) $arg->type as $type) {
            $interface = $this->getExpressionTypeInterface($type);
            $nativeTypes = array_merge($nativeTypes, [$interface], $this->expressions[$interface]->types);
        }

        $docTypes = $nativeTypes = array_unique($nativeTypes);
        $listCheck = false;
        $use = [];

        foreach ($nativeTypes as $key => $typeName) {
            if ($typeName === 'list') {
                $listCheck = true;
                $nativeTypes[$key] = 'array';
                $docTypes[$key] = 'list<Expression|mixed>';
                $use[] = '\\' . ExpressionInterface::class;
                continue;
            }

            if (interface_exists($typeName) || class_exists($typeName)) {
                $use[] = $nativeTypes[$key] = '\\' . $typeName;
                //$nativeTypes[$key] = $docTypes[$key] = '\\' . $typeName;
                $docTypes[$key] = $this->splitNamespaceAndClassName($typeName)[1];
                // A union cannot contain both object and a class type
                if (in_array('object', $nativeTypes, true)) {
                    unset($nativeTypes[$key]);
                }
            }
        }

        if ($arg->isOptional) {
            $nativeTypes[] = 'null';
            $docTypes[] = 'null';
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
