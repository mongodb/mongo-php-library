<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\ClassType;

use function array_pop;
use function assert;
use function dirname;
use function explode;
use function implode;

/** @internal */
final class FactoryClassGenerator extends AbstractGenerator
{
    public function createClassesForObjects(array $objects): void
    {
        // We use the namespace as class name
        $namespaceParts = explode('\\', $this->definition->namespace);
        $className = array_pop($namespaceParts);
        $namespace = implode('\\', $namespaceParts);

        $this->createFileForClass(
            dirname($this->definition->filePath),
            $this->createFactoryClass($objects, $className),
            $namespace,
        );
    }

    private function createFactoryClass(array $objects, string $className): ClassType
    {
        $class = new ClassType($className);
        $class->setFinal();
        $class->addMethod('__construct')->setPrivate()
            ->setComment('This class cannot be instantiated.');

        foreach ($objects as $object) {
            assert($object instanceof OperatorDefinition);
            $operatorClassName = '\\' . $this->definition->namespace . '\\' . $this->getClassName($object);

            $method = $class->addMethod($object->name);
            $method->setStatic();
            $method->addBody('return new ' . $operatorClassName . '(');
            foreach ($object->arguments as $argument) {
                ['native' => $nativeType, 'doc' => $docType] = $this->generateTypeString($argument);

                $parameter = $method->addParameter($argument->name);
                $parameter->setType($nativeType);
                if ($argument->isVariadic) {
                    $method->setVariadic();
                }

                $method->addComment('@param ' . $docType . ' $' . $argument->name);
                $method->addBody('    $' . $argument->name . ',');
            }

            $method->addBody(');');
            $method->addComment('@return ' . $operatorClassName);
            $method->setReturnType($operatorClassName);
        }

        return $class;
    }

    public function createClassForObject(object $object): ClassType
    {
        // Not used
    }
}
