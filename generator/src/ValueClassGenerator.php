<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\ClassType;
use RuntimeException;

use function assert;
use function interface_exists;
use function ucfirst;

/**
 * Generates a value object class for stages and operators.
 */
class ValueClassGenerator extends AbstractGenerator
{
    public function createClassForObject(object $object): ClassType
    {
        assert($object instanceof OperatorDefinition);

        $class = new ClassType($this->getClassName($object));
        $class->setImplements($this->getInterfaces($object));

        $constuctor = $class->addMethod('__construct');

        foreach ($object->arguments as $argument) {
            ['native' => $nativeType, 'doc' => $docType] = $this->generateTypeString($argument);

            // Property
            $propertyComment = '';
            $property = $class->addProperty($argument->name);
            if ($argument->isVariadic) {
                $property->setType('array');
                $propertyComment .= '@param list<' . $docType . '> $' . $argument->name;
            } else {
                $property->setType($nativeType);
            }

            $property->setComment($propertyComment);

            // Constructor
            $constuctorParam = $constuctor->addParameter($argument->name);
            $constuctorParam->setType($nativeType);
            if ($argument->isVariadic) {
                $constuctor->setVariadic();

                if ($argument->variadicMin !== null) {
                    $constuctor->addBody(<<<PHP
                    if (\count(\${$argument->name}) < {$argument->variadicMin}) {
                        throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', $argument->variadicMin, \count(\${$argument->name})));
                    }

                    PHP);
                }
            }

            $constuctor->addComment('@param ' . $docType . ' $' . $argument->name);
            $constuctor->addBody('$this->' . $argument->name . ' = $' . $argument->name . ';');
        }

        return $class;
    }

    private function getInterfaces(OperatorDefinition $definition): array
    {
        if ($definition->type === null) {
            return [];
        }

        $interface = 'MongoDB\\Builder\\Expression\\' . ucfirst($definition->type);
        if (! interface_exists($interface)) {
            throw new RuntimeException('Interface ' . $interface . ' does not exist');
        }

        return [$interface];
    }
}
