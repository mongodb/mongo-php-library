<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type;

use function array_map;
use function sprintf;
use function str_contains;

/**
 * Generates a value object class for expressions
 */
class ExpressionClassGenerator extends AbstractGenerator
{
    public function generate(ExpressionDefinition $definition): void
    {
        $this->writeFile($this->createClassOrInterface($definition));
    }

    public function createClassOrInterface(ExpressionDefinition $definition): PhpNamespace
    {
        [$namespace, $className] = $this->splitNamespaceAndClassName($definition->name);
        $namespace = new PhpNamespace($namespace);
        foreach ($definition->implements as $interface) {
            $namespace->addUse($interface);
        }

        $types = array_map(
            fn (string $type): string => match ($type) {
                'list' => 'array',
                default => $type,
            },
            $definition->types,
        );

        if ($definition->class) {
            $class = $namespace->addClass($className);
            $class->setImplements($definition->implements);
            if ($definition->extends) {
                $class->setExtends($definition->extends);
            }

            // Replace with promoted property in PHP 8
            $propertyType = Type::union(...$types);
            $class->addProperty('expression')
                ->setType($propertyType)
                ->setPublic();

            $constructor = $class->addMethod('__construct');
            $constructor->addParameter('expression')->setType($propertyType);
            $constructor->addBody('$this->expression = $expression;');
        } else {
            $class = $namespace->addInterface($className);
            $class->setExtends($definition->implements);
        }

        // @todo add namespace use for types classes & interfaces
        $types = array_map(
            function (string $type): string|Literal {
                if (str_contains($type, '\\')) {
                    return new Literal(sprintf('\\%s::class', $type));
                }

                return $type;
            },
            $definition->types,
        );

        $class->addConstant('ACCEPTED_TYPES', $types);

        return $namespace;
    }
}
