<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use MongoDB\Builder\Pipeline;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use MongoDB\Tests\Builder\PipelineTestCase;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type;
use RuntimeException;
use Throwable;

use function basename;
use function json_encode;
use function ksort;
use function sprintf;
use function str_replace;
use function ucwords;

use const JSON_PRETTY_PRINT;

/**
 * Generates a tests for all operators.
 */
class OperatorTestGenerator extends OperatorGenerator
{
    private const DATA_ENUM = 'Pipelines';

    public function generate(GeneratorDefinition $definition): void
    {
        $dataNamespace = $this->createExpectedClass($definition);

        foreach ($this->getOperators($definition) as $operator) {
            // Skip operators without tests
            if (! $operator->tests) {
                continue;
            }

            try {
                $this->writeFile($this->createClass($definition, $operator, $dataNamespace->getClasses()[self::DATA_ENUM]), false);
            } catch (Throwable $e) {
                throw new RuntimeException(sprintf('Failed to generate class for operator "%s"', $operator->name), 0, $e);
            }
        }

        $this->writeFile($dataNamespace);
    }

    public function createExpectedClass(GeneratorDefinition $definition): PhpNamespace
    {
        $dataNamespace = str_replace('MongoDB', 'MongoDB\\Tests', $definition->namespace);

        $namespace = new PhpNamespace($dataNamespace);
        $enum = $namespace->addEnum(self::DATA_ENUM);
        $enum->setType('string');

        return $namespace;
    }

    public function createClass(GeneratorDefinition $definition, OperatorDefinition $operator, EnumType $dataEnum): PhpNamespace
    {
        $testNamespace = str_replace('MongoDB', 'MongoDB\\Tests', $definition->namespace);
        $testClass = $this->getOperatorClassName($definition, $operator) . 'Test';

        $namespace = $this->readFile($testNamespace, $testClass)?->getNamespaces()[$testNamespace] ?? null;
        $namespace ??= new PhpNamespace($testNamespace);

        $class = $namespace->getClasses()[$testClass] ?? null;
        $class ??= $namespace->addClass($testClass);
        $namespace->addUse(PipelineTestCase::class);
        $class->setExtends(PipelineTestCase::class);
        $namespace->addUse(Pipeline::class);
        $class->setComment('Test ' . $operator->name . ' ' . basename($definition->configFiles));

        foreach ($operator->tests as $test) {
            $testName = 'test' . str_replace([' ', '-'], '', ucwords(str_replace('$', '', $test->name)));
            $caseName = str_replace([' ', '-'], '', ucwords(str_replace('$', '', $operator->name . ' ' . $test->name)));

            $case = $dataEnum->addCase($caseName, new Literal('<<<\'JSON\'' . "\n" . json_encode($test->pipeline, JSON_PRETTY_PRINT) . "\n" . 'JSON'));
            $case->setComment($test->name);
            if ($test->link) {
                $case->addComment('');
                $case->addComment('@see ' . $test->link);
            }

            $caseName = self::DATA_ENUM . '::' . $caseName;

            if ($class->hasMethod($testName)) {
                $testMethod = $class->getMethod($testName);
            } else {
                $testMethod = $class->addMethod($testName);
                $testMethod->setBody(<<<PHP
                \$pipeline = new Pipeline();

                \$this->assertSamePipeline({$caseName}, \$pipeline);
                PHP);
            }

            $testMethod->setPublic();
            $testMethod->setReturnType(Type::Void);
        }

        $methods = $class->getMethods();
        ksort($methods);
        $class->setMethods($methods);

        return $namespace;
    }
}
