<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Command;

use MongoDB\CodeGenerator\Definition\ExpressionDefinition;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\ExpressionClassGenerator;
use MongoDB\CodeGenerator\ExpressionFactoryGenerator;
use MongoDB\CodeGenerator\OperatorGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function basename;
use function class_exists;
use function is_array;
use function sprintf;

#[AsCommand(name: 'generate', description: 'Generate code for mongodb/mongodb library')]
final class GenerateCommand extends Command
{
    public function __construct(
        private string $rootDir,
        private string $configDir,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating code for mongodb/mongodb library');

        $this->generateExpressionClasses($output);

        $config = require $this->configDir . '/operators.php';
        assert(is_array($config));

        foreach ($config as $key => $def) {
            assert(is_array($def));
            $this->generate($def, $output);
        }

        return Command::SUCCESS;
    }

    private function generateExpressionClasses(OutputInterface $output): void
    {
        $output->writeln('Generating expression classes');

        $config = require $this->configDir . '/expressions.php';
        assert(is_array($config));

        $definitions = [];
        $generator = new ExpressionClassGenerator($this->rootDir);
        foreach ($config as $name => $def) {
            assert(is_array($def));
            $definitions[$name] = $def = new ExpressionDefinition($name, ...$def);
            $generator->generate($def);
        }

        $generator = new ExpressionFactoryGenerator($this->rootDir);
        $generator->generate($definitions);
    }

    private function generate(array $def, OutputInterface $output): void
    {
        $definition = new GeneratorDefinition(...$def);

        $output->writeln(sprintf('Generating classes for %s with %s', basename($definition->configFile), $definition->generatorClass));

        if (! class_exists($definition->generatorClass)) {
            $output->writeln(sprintf('Generator class %s does not exist', $definition->generatorClass));

            return;
        }

        $generatorClass = $definition->generatorClass;
        $generator = new $generatorClass($this->rootDir);
        assert($generator instanceof OperatorGenerator);
        $generator->generate($definition);
    }
}
