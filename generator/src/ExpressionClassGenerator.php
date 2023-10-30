<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use LogicException;
use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\PhpObject;
use MongoDB\Exception\InvalidArgumentException;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type;
use RuntimeException;
use Throwable;

use function array_map;
use function ucfirst;
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

        if ($definition->generate === PhpObject::PhpClass) {
            $class = $namespace->addClass($className);
            $class->setImplements($definition->implements);
            $class->setExtends($definition->extends);

            // Replace with promoted property in PHP 8.1
            $propertyType = Type::union(...$types);
            $class->addProperty('name')
                ->setType($propertyType)
                ->setReadOnly()
                ->setPublic();

            $constructor = $class->addMethod('__construct');
            $constructor->addParameter('name')->setType($propertyType);

            $namespace->addUse(InvalidArgumentException::class);
            $namespace->addUseFunction('sprintf');
            $namespace->addUseFunction('str_starts_with');
            $constructor->addBody(<<<PHP
            if (str_starts_with(\$name, '$')) {
                throw new InvalidArgumentException(sprintf('Name cannot start with a dollar sign: "%s"', \$name));
            }

            PHP);

            $constructor->addBody('$this->name = $name;');
        } elseif ($definition->generate === PhpObject::PhpInterface) {
            $interface = $namespace->addInterface($className);
            $interface->setExtends($definition->implements);
        } elseif ($definition->generate === PhpObject::PhpEnum) {
            $enum = $namespace->addEnum($className);
            $enum->setType('string');
            array_map(
                fn (string $case) => $enum->addCase(ucfirst($case), $case),
                $definition->values,
            );
        } else {
            throw new LogicException('Unknown generate type: ' . var_export($definition->generate, true));
        }

        return $namespace;
    }
}
