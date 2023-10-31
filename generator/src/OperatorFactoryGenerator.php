<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use MongoDB\CodeGenerator\Definition\VariadicType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\TraitType;
use RuntimeException;
use Throwable;

use function implode;
use function ltrim;
use function rtrim;
use function sprintf;
use function str_replace;
use function strcasecmp;
use function usort;

final class OperatorFactoryGenerator extends OperatorGenerator
{
    public function generate(GeneratorDefinition $definition): void
    {
        $this->writeFile($this->createFactoryTrait($definition));
    }

    private function createFactoryTrait(GeneratorDefinition $definition): PhpNamespace
    {
        $namespace = new PhpNamespace($definition->namespace);
        $trait = $namespace->addTrait('FactoryTrait');
        $trait->addComment('@internal');

        // Pedantry requires methods to be ordered alphabetically
        $operators = $this->getOperators($definition);
        usort($operators, fn (OperatorDefinition $a, OperatorDefinition $b) => strcasecmp($a->name, $b->name));

        foreach ($operators as $operator) {
            try {
                $this->addMethod($definition, $operator, $namespace, $trait);
            } catch (Throwable $e) {
                throw new RuntimeException(sprintf('Failed to generate class for operator "%s"', $operator->name), 0, $e);
            }
        }

        return $namespace;
    }

    private function addMethod(GeneratorDefinition $definition, OperatorDefinition $operator, PhpNamespace $namespace, TraitType $trait): void
    {
        $operatorClassName = '\\' . $definition->namespace . '\\' . $this->getOperatorClassName($definition, $operator);
        $namespace->addUse($operatorClassName);

        $method = $trait->addMethod(ltrim($operator->name, '$'));
        $method->setStatic();
        $method->addComment($operator->description);
        $method->addComment('@see ' . $operator->link);
        $args = [];
        foreach ($operator->arguments as $argument) {
            $type = $this->getAcceptedTypes($argument);
            foreach ($type->use as $use) {
                $namespace->addUse($use);
            }

            $parameter = $method->addParameter($argument->name);
            $parameter->setType($type->native);
            if ($argument->variadic) {
                if ($argument->variadic === VariadicType::Array) {
                    // Warn that named arguments are not supported
                    // @see https://psalm.dev/docs/running_psalm/issues/NamedArgumentNotAllowed/
                    $method->addComment('@no-named-arguments');
                }

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
