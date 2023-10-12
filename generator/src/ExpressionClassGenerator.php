<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use LogicException;
use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\Generate;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type;
use RuntimeException;
use Throwable;

use function array_map;
use function var_export;

/**
 * Generates a value object class for expressions
 */
class ExpressionClassGenerator extends AbstractGenerator
{
    public function generate(ExpressionDefinition $definition): void
    {
        if (! $definition->generate) {
            return;
        }

        try {
            $this->writeFile($this->createClassOrInterface($definition));
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to generate expression class for ' . $definition->name, 0, $e);
        }
    }

    public function createClassOrInterface(ExpressionDefinition $definition): PhpNamespace
    {
        [$namespace, $className] = $this->splitNamespaceAndClassName($definition->returnType);
        $namespace = new PhpNamespace($namespace);
        foreach ($definition->implements as $interface) {
            $namespace->addUse($interface);
        }

        $types = array_map(
            fn (string $type): string => match ($type) {
                'list' => 'array',
                default => $type,
            },
            $definition->acceptedTypes,
        );

        if ($definition->generate === Generate::PhpClass) {
            $class = $namespace->addClass($className);
            $class->setReadOnly();
            $class->setImplements($definition->implements);
            $class->setExtends($definition->extends);

            // Replace with promoted property in PHP 8.1
            $propertyType = Type::union(...$types);
            $class->addProperty('name')
                ->setType($propertyType)
                ->setPublic();

            $constructor = $class->addMethod('__construct');
            $constructor->addParameter('name')->setType($propertyType);
            $constructor->addBody('$this->name = $name;');
        } elseif ($definition->generate === Generate::PhpInterface) {
            $class = $namespace->addInterface($className);
            $class->setExtends($definition->implements);
        } else {
            throw new LogicException('Unknown generate type: ' . var_export($definition->generate, true));
        }

        return $namespace;
    }
}
