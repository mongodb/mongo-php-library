<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\PhpObject;
use Nette\PhpGenerator\PhpNamespace;

use function lcfirst;
use function usort;

final class ExpressionFactoryGenerator extends AbstractGenerator
{
    /** @param array<string, ExpressionDefinition> $expressions */
    public function generate(array $expressions): void
    {
        $this->writeFile($this->createFactoryClass($expressions));
    }

    /** @param array<class-string, ExpressionDefinition> $expressions */
    private function createFactoryClass(array $expressions): PhpNamespace
    {
        $namespace = new PhpNamespace('MongoDB\\Builder\\Expression');
        $trait = $namespace->addTrait('ExpressionFactoryTrait');
        $trait->addComment('@internal');

        // Pedantry requires methods to be ordered alphabetically
        usort($expressions, fn (ExpressionDefinition $a, ExpressionDefinition $b) => $a->name <=> $b->name);

        foreach ($expressions as $expression) {
            if ($expression->generate !== PhpObject::PhpClass) {
                continue;
            }

            $namespace->addUse($expression->returnType);
            $expressionShortClassName = $this->splitNamespaceAndClassName($expression->returnType)[1];

            $method = $trait->addMethod(lcfirst($expressionShortClassName));
            $method->setStatic();
            $method->addParameter('name')->setType('string');
            $method->addBody('return new ' . $expressionShortClassName . '($name);');
            $method->setReturnType($expression->returnType);
        }

        return $namespace;
    }
}
