<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\Generate;
use Nette\PhpGenerator\PhpNamespace;

use function lcfirst;
use function usort;

final class ExpressionFactoryGenerator extends AbstractGenerator
{
    /** @param array<class-string, ExpressionDefinition> $definitions */
    public function generate(array $expressions): void
    {
        $this->writeFile($this->createFactoryClass($expressions));
    }

    /** @param array<class-string, ExpressionDefinition> $expressions */
    private function createFactoryClass(array $expressions): PhpNamespace
    {
        $namespace = new PhpNamespace('MongoDB\\Builder');
        $class = $namespace->addClass('Expression');
        $class->setFinal();

        // Pedantry requires methods to be ordered alphabetically
        usort($expressions, fn (ExpressionDefinition $a, ExpressionDefinition $b) => $a->name <=> $b->name);

        foreach ($expressions as $expression) {
            if ($expression->generate !== Generate::PhpClass) {
                continue;
            }

            $namespace->addUse($expression->returnType);
            $expressionShortClassName = $this->splitNamespaceAndClassName($expression->returnType)[1];

            $method = $class->addMethod(lcfirst($expressionShortClassName));
            $method->setStatic();
            $method->addParameter('expression')->setType('string');
            $method->addBody('return new ' . $expressionShortClassName . '($expression);');
            $method->setReturnType($expression->returnType);
        }

        // Pedantry requires private methods to be at the end
        $class->addMethod('__construct')->setPrivate()
            ->setComment('This class cannot be instantiated.');

        return $namespace;
    }
}
