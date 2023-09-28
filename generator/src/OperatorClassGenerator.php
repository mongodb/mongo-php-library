<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\Builder\Stage\StageInterface;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\PhpNamespace;
use RuntimeException;

use function interface_exists;
use function sprintf;

/**
 * Generates a value object class for stages and operators.
 */
class OperatorClassGenerator extends OperatorGenerator
{
    public function generate(GeneratorDefinition $definition): void
    {
        foreach ($this->getOperators($definition) as $operator) {
            $this->writeFile($this->createClass($definition, $operator));
        }
    }

    public function createClass(GeneratorDefinition $definition, OperatorDefinition $operator): PhpNamespace
    {
        $namespace = new PhpNamespace($definition->namespace);

        $interfaces = $this->getInterfaces($operator);
        foreach ($interfaces as $interface) {
            $namespace->addUse($interface);
        }

        $class = $namespace->addClass($this->getOperatorClassName($definition, $operator));
        $class->setImplements($interfaces);

        // Expose operator metadata as constants
        // @todo move to encoder class
        $class->addConstant('NAME', '$' . $operator->name);
        $class->addConstant('ENCODE', $operator->encode ?? 'single');

        $constuctor = $class->addMethod('__construct');
        foreach ($operator->arguments as $argument) {
            $type = $this->generateExpressionTypes($argument);
            foreach ($type->use as $use) {
                $namespace->addUse($use);
            }

            // Property
            $propertyComment = '';
            $property = $class->addProperty($argument->name);
            if ($argument->isVariadic) {
                $property->setType('array');
                $propertyComment .= '@param list<' . $type->doc . '> ...$' . $argument->name;
            } else {
                $property->setType($type->native);
            }

            $property->setComment($propertyComment);

            // Constructor
            $constuctorParam = $constuctor->addParameter($argument->name);
            $constuctorParam->setType($type->native);
            if ($argument->isVariadic) {
                $constuctor->setVariadic();

                if ($argument->variadicMin > 0) {
                    $constuctor->addBody(<<<PHP
                    if (\count(\${$argument->name}) < {$argument->variadicMin}) {
                        throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', $argument->variadicMin, \count(\${$argument->name})));
                    }

                    PHP);
                }
            } elseif ($argument->isOptional) {
                $constuctorParam->setDefaultValue(null);
            }

            $constuctor->addComment('@param ' . $type->doc . ' $' . $argument->name);

            // List type must be validated with array_is_list()
            if ($type->list) {
                $constuctor->addBody(<<<PHP
                if (\is_array(\${$argument->name}) && ! \array_is_list(\${$argument->name})) {
                    throw new \InvalidArgumentException(\sprintf('Expected \${$argument->name} argument to be a list, got an associative array.'));
                }
                PHP);
            }

            // Set property from constructor argument
            $constuctor->addBody('$this->' . $argument->name . ' = $' . $argument->name . ';');
        }

        return $namespace;
    }

    /**
     * Operator classes interfaces are defined by their return type as a MongoDB expression.
     */
    private function getInterfaces(OperatorDefinition $definition): array
    {
        if ($definition->type === null) {
            return [];
        }

        if ($definition->type === 'stage') {
            return ['\\' . StageInterface::class];
        }

        $interface = $this->getExpressionTypeInterface($definition->type);
        if (! interface_exists($interface)) {
            throw new RuntimeException(sprintf('"%s" is not an interface.', $interface));
        }

        return [$interface];
    }
}
