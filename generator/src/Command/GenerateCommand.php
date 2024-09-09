<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Command;

use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\ExpressionClassGenerator;
use MongoDB\CodeGenerator\ExpressionFactoryGenerator;
use MongoDB\CodeGenerator\OperatorGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_key_exists;
use function assert;
use function basename;
use function is_a;
use function is_array;
use function sprintf;

final class GenerateCommand extends Command
{
    public function __construct(
        private string $rootDir,
        private string $configDir,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate code for mongodb/mongodb library');
        $this->setHelp('Generate code for mongodb/mongodb library');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating code for mongodb/mongodb library');

        $expressions = $this->generateExpressionClasses($output);
        $this->generateOperatorClasses($expressions, $output);

        return Command::SUCCESS;
    }

    /** @return array<string, ExpressionDefinition> */
    private function generateExpressionClasses(OutputInterface $output): array
    {
        $output->writeln('Generating expression classes');

        $config = require $this->configDir . '/expressions.php';
        assert(is_array($config));

        $definitions = [];
        $generator = new ExpressionClassGenerator($this->rootDir);
        foreach ($config as $name => $def) {
            assert(is_array($def));
            assert(! array_key_exists($name, $definitions), sprintf('Duplicate expression name "%s".', $name));
            $definitions[$name] = $def = new ExpressionDefinition($name, ...$def);
            $generator->generate($def);
        }

        $generator = new ExpressionFactoryGenerator($this->rootDir);
        $generator->generate($definitions);

        return $definitions;
    }

    /** @param array<string, ExpressionDefinition> $expressions */
    private function generateOperatorClasses(array $expressions, OutputInterface $output): void
    {
        $config = require $this->configDir . '/definitions.php';
        assert(is_array($config));

        foreach ($config as $def) {
            assert(is_array($def));
            $definition = new GeneratorDefinition(...$def);

            foreach ($definition->generators as $generatorClass) {
                $output->writeln(sprintf('Generating classes for %s with %s', basename($definition->configFiles), $generatorClass));
                assert(is_a($generatorClass, OperatorGenerator::class, true));
                $generator = new $generatorClass($this->rootDir, $expressions);
                $generator->generate($definition);
            }
        }
    }
}
