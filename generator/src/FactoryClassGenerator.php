<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use Nette\PhpGenerator\ClassType;

use Nette\PhpGenerator\PhpNamespace;
use function array_pop;
use function assert;
use function explode;
use function implode;

use const PHP_EOL;

/** @internal */
final class FactoryClassGenerator extends AbstractGenerator
{
    public function createClassesForObjects(array $objects): void
    {
        $this->createFileForClass(
            $this->definition->filePath,
            $this->createBuilderClass($objects),
        );
    }

    private function createBuilderClass(array $objects): ClassType
    {
        // We use the namespace as class name
        $namespaceParts = explode('\\', $this->definition->namespace);
        $className = array_pop($namespaceParts);
        $namespace = implode('\\', $namespaceParts);

        $class = new ClassType($className, new PhpNamespace($namespace));
        $class->setFinal();

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
            $method->setReturnType($operatorClassName);

            $method->addComment('@return ' . $operatorClassName);
        }

        return $class;
    }

    public function createClassForObject(object $object): ClassType
    {
        // Not used
    }
}
