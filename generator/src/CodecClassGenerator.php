<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\ClassType;

use function assert;

use const PHP_EOL;

/**
 * Codec classes translates the operator value object into a BSON representation.
 */
class CodecClassGenerator extends OperatorGenerator
{
    public function createClassForObject(object $object): ClassType
    {
        assert($object instanceof OperatorDefinition);

        $class = new ClassType($this->getClassName($object));

        $constuctor = $class->addMethod('__construct');

        foreach ($object->arguments as $argument) {
            ['native' => $nativeType, 'doc' => $docType] = $this->getAcceptedTypes($argument);

            // Property
            $propertyComment = '';
            $property = $class->addProperty($argument->name);
            if ($argument->isVariadic) {
                $property->setType('array');
                $propertyComment .= '@param list<' . $docType . '> $' . $argument->name . PHP_EOL;
            } else {
                $property->setType($nativeType);
            }

            $property->setComment($propertyComment);

            // Constructor
            $constuctorParam = $constuctor->addParameter($argument->name);
            $constuctorParam->setType($nativeType);
            if ($argument->isVariadic) {
                $constuctor->setVariadic();
            }

            $constuctor->addComment('@param ' . $docType . ' $' . $argument->name . PHP_EOL);
            $constuctor->addBody('$this->' . $argument->name . ' = $' . $argument->name . ';' . PHP_EOL);
        }

        return $class;
    }

    public function generate(GeneratorDefinition $definition): void
    {
        // TODO: Implement generate() method.
    }
}
