<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;

use function implode;
use function ltrim;
use function str_replace;
use function usort;

final class OperatorFactoryGenerator extends OperatorGenerator
{
    public function generate(GeneratorDefinition $definition): void
    {
        $this->writeFile($this->createFactoryClass($definition));
    }

    private function createFactoryClass(GeneratorDefinition $definition): PhpNamespace
    {
        // Use the operators namespace as factory class name.
        [$namespace, $className] = $this->splitNamespaceAndClassName($definition->namespace);
        $namespace = new PhpNamespace($namespace);
        $class = $namespace->addClass($className);
        $class->setFinal();

        // Pedantry requires methods to be ordered alphabetically
        $operators = $this->getOperators($definition);
        usort($operators, fn (OperatorDefinition $a, OperatorDefinition $b) => $a->name <=> $b->name);

        foreach ($operators as $operator) {
            $operatorClassName = '\\' . $definition->namespace . '\\' . $this->getOperatorClassName($definition, $operator);
            $namespace->addUse($operatorClassName);

            $method = $class->addMethod($operator->name);
            $method->setStatic();
            $args = [];
            foreach ($operator->arguments as $argument) {
                $type = $this->generateExpressionTypes($argument);
                foreach ($type->use as $use) {
                    $namespace->addUse($use);
                }

                $parameter = $method->addParameter($argument->name);
                $parameter->setType($type->native);
                if ($argument->isVariadic) {
                    $method->setVariadic();
                    $method->addComment('@param ' . $type->doc . ' ...$' . $argument->name);
                    $args[] = '...$' . $argument->name;
                } else {
                    if ($argument->isOptional) {
                        $parameter->setDefaultValue(new Literal('Optional::Undefined'));
                    }

                    $method->addComment('@param ' . $type->doc . ' $' . $argument->name);
                    $args[] = '$' . $argument->name;
                }
            }

            $operatorShortClassName = ltrim(str_replace($definition->namespace, '', $operatorClassName), '\\');
            $method->addBody('return new ' . $operatorShortClassName . '(' . implode(', ', $args) . ');');
            $method->setReturnType($operatorClassName);
        }

        // Pedantry requires private methods to be at the end
        $class->addMethod('__construct')->setPrivate()
            ->setComment('This class cannot be instantiated.');

        return $namespace;
    }
}
