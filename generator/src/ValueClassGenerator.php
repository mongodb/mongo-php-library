<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\ClassType;

use function assert;

use const PHP_EOL;

/**
 * Generates a value object class for stages and operators.
 */
class ValueClassGenerator extends AbstractGenerator
{
    public function createClassForObject(object $object): ClassType
    {
        assert($object instanceof OperatorDefinition);

        $class = new ClassType($this->getClassName($object));

        $constuctor = $class->addMethod('__construct')
            ->setPrivate()
            ->setBody('/* intentionally empty */');
        $constuctorBody = '';
        $constuctorComment = '';

        foreach ($object->arguments as $argument) {
            ['native' => $nativeType, 'doc' => $docType] = $this->generateTypeString($argument);

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

            $constuctorComment .= '@param ' . $docType . ' $' . $argument->name . PHP_EOL;
            $constuctorBody .= '$this->' . $argument->name . ' = $' . $argument->name . ';' . PHP_EOL;
        }

        $constuctor->setComment($constuctorComment);
        $constuctor->setBody($constuctorBody);

        return $class;
    }
}
