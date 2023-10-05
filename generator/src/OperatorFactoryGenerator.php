<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use RuntimeException;
use Throwable;

use function implode;
use function ltrim;
use function rtrim;
use function sprintf;
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
            try {
                $this->addMethod($definition, $operator, $namespace, $class);
            } catch (Throwable $e) {
                throw new RuntimeException(sprintf('Failed to generate class for operator "%s"', $operator->name), 0, $e);
            }
        }

        // Pedantry requires private methods to be at the end
        $class->addMethod('__construct')->setPrivate()
            ->setComment('This class cannot be instantiated.');

        return $namespace;
    }

    private function addMethod(GeneratorDefinition $definition, OperatorDefinition $operator, PhpNamespace $namespace, ClassType $class): void
    {
        $operatorClassName = '\\' . $definition->namespace . '\\' . $this->getOperatorClassName($definition, $operator);
        $namespace->addUse($operatorClassName);

        $method = $class->addMethod(ltrim($operator->name, '$'));
        $method->setStatic();
        $method->addComment($operator->description);
        $method->addComment('@see ' . $operator->link);
        $args = [];
        foreach ($operator->arguments as $argument) {
            $type = $this->generateExpressionTypes($argument);
            foreach ($type->use as $use) {
                $namespace->addUse($use);
            }

            $parameter = $method->addParameter($argument->name);
            $parameter->setType($type->native);
            if ($argument->variadic) {
                $method->setVariadic();
                $method->addComment('@param ' . $type->doc . ' ...$' . $argument->name . rtrim(' ' . $argument->description));
                $args[] = '...$' . $argument->name;
            } else {
                if ($argument->optional) {
                    $parameter->setDefaultValue(new Literal('Optional::Undefined'));
                }

                $method->addComment('@param ' . $type->doc . ' $' . $argument->name . rtrim(' ' . $argument->description));
                $args[] = '$' . $argument->name;
            }
        }

        $operatorShortClassName = ltrim(str_replace($definition->namespace, '', $operatorClassName), '\\');
        $method->addBody('return new ' . $operatorShortClassName . '(' . implode(', ', $args) . ');');
        $method->setReturnType($operatorClassName);
    }
}
