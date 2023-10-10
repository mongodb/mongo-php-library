<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\Builder\Encode;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use MongoDB\CodeGenerator\Definition\VariadicType;
use MongoDB\Exception\InvalidArgumentException;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use RuntimeException;
use stdClass;
use Throwable;

use function assert;
use function interface_exists;
use function rtrim;
use function sprintf;

/**
 * Generates a value object class for stages and operators.
 */
class OperatorClassGenerator extends OperatorGenerator
{
    public function generate(GeneratorDefinition $definition): void
    {
        foreach ($this->getOperators($definition) as $operator) {
            try {
                $this->writeFile($this->createClass($definition, $operator));
            } catch (Throwable $e) {
                throw new RuntimeException(sprintf('Failed to generate class for operator "%s"', $operator->name), 0, $e);
            }
        }
    }

    public function createClass(GeneratorDefinition $definition, OperatorDefinition $operator): PhpNamespace
    {
        $namespace = new PhpNamespace($definition->namespace);

        $interfaces = $this->getInterfaces($operator);
        foreach ($interfaces as $interface) {
            $namespace->addUse($interface);
        }

        $class = $namespace->addClass($this->getOperatorClassName($definition, $operator));
        $class->setImplements($interfaces);

        // Expose operator metadata as constants
        // @todo move to encoder class
        $namespace->addUse('\\' . Encode::class);
        $class->addComment($operator->description);
        $class->addComment('@see ' . $operator->link);
        $class->addConstant('NAME', $operator->name);
        $class->addConstant('ENCODE', $operator->encode);

        $constuctor = $class->addMethod('__construct');
        foreach ($operator->arguments as $argument) {
            $type = $this->getAcceptedTypes($argument);
            foreach ($type->use as $use) {
                $namespace->addUse($use);
            }

            $property = $class->addProperty($argument->name);
            $constuctorParam = $constuctor->addParameter($argument->name);
            $constuctorParam->setType($type->native);

            if ($argument->variadic) {
                $constuctor->setVariadic();
                $constuctor->addComment('@param ' . $type->doc . ' ...$' . $argument->name . rtrim(' ' . $argument->description));

                if ($argument->variadicMin !== null) {
                    $constuctor->addBody(<<<PHP
                    if (\count(\${$argument->name}) < {$argument->variadicMin}) {
                        throw new \InvalidArgumentException(\sprintf('Expected at least %d values for \${$argument->name}, got %d.', {$argument->variadicMin}, \count(\${$argument->name})));
                    }
                    PHP);
                }

                if ($argument->variadic === VariadicType::Array) {
                    $property->setType('array');
                    $property->addComment('@param list<' . $type->doc . '> ...$' . $argument->name . rtrim(' ' . $argument->description));
                    // Warn that named arguments are not supported
                    // @see https://psalm.dev/docs/running_psalm/issues/NamedArgumentNotAllowed/
                    $constuctor->addComment('@no-named-arguments');
                    $namespace->addUseFunction('array_is_list');
                    $namespace->addUse(InvalidArgumentException::class);
                    $constuctor->addBody(<<<PHP
                    if (! array_is_list(\${$argument->name})) {
                        throw new InvalidArgumentException('Expected \${$argument->name} arguments to be a list (array), named arguments are not supported');
                    }
                    PHP);
                } elseif ($argument->variadic === VariadicType::Object) {
                    $namespace->addUse(stdClass::class);
                    $property->setType(stdClass::class);
                    $property->addComment('@param stdClass<' . $type->doc . '> ...$' . $argument->name . rtrim(' ' . $argument->description));
                    $namespace->addUseFunction('is_string');
                    $namespace->addUse(InvalidArgumentException::class);
                    $constuctor->addBody(<<<PHP
                    foreach(\${$argument->name} as \$key => \$value) {
                        if (! is_string(\$key)) {
                            throw new InvalidArgumentException('Expected \${$argument->name} arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
                        }
                    }
                    \${$argument->name} = (object) \${$argument->name};
                    PHP);
                }
            } else {
                // Non-variadic arguments
                $property->addComment('@param ' . $type->doc . ' $' . $argument->name . rtrim(' ' . $argument->description));
                $property->setType($type->native);
                $constuctor->addComment('@param ' . $type->doc . ' $' . $argument->name . rtrim(' ' . $argument->description));

                if ($argument->optional) {
                    // We use a special Optional::Undefined type to differentiate between null and undefined
                    $constuctorParam->setDefaultValue(new Literal('Optional::Undefined'));
                }

                // List type must be validated with array_is_list()
                if ($type->list) {
                    $namespace->addUseFunction('is_array');
                    $namespace->addUseFunction('array_is_list');
                    $namespace->addUse(InvalidArgumentException::class);
                    $constuctor->addBody(<<<PHP
                    if (is_array(\${$argument->name}) && ! array_is_list(\${$argument->name})) {
                        throw new InvalidArgumentException('Expected \${$argument->name} argument to be a list, got an associative array.');
                    }

                    PHP);
                }
            }

            // Set property from constructor argument
            $constuctor->addBody('$this->' . $argument->name . ' = $' . $argument->name . ';');
        }

        return $namespace;
    }

    /**
     * Operator classes interfaces are defined by their return type as a MongoDB expression.
     */
    private function getInterfaces(OperatorDefinition $definition): array
    {
        $interfaces = [];

        foreach ($definition->type as $type) {
            $interfaces[] = $interface = $this->getType($type)->returnType;
            assert(interface_exists($interface), sprintf('"%s" is not an interface.', $interface));
        }

        return $interfaces;
    }
}
