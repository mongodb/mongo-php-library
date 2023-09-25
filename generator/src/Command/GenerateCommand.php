<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Command;

use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\YamlReader;
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
    private YamlReader $yamlReader;

    public function __construct(
        private string $configFile,
    ) {
        parent::__construct();

        $this->yamlReader = new YamlReader();
    }

    public function configure(): void
    {
        $this->addOption('force', 'f', null, 'Force generation of all files');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating code for mongodb/mongodb library');

        $config = require $this->configFile;
        assert(is_array($config));

        // @todo This is a hack to get the first pipeline operator config
        foreach ($config as $key => $def) {
            assert(is_array($def));
            $this->generate(new GeneratorDefinition(...$def), $output);
        }

        return Command::SUCCESS;
    }

    private function generate(GeneratorDefinition $definition, OutputInterface $output): void
    {
        $output->writeln(sprintf('Generating code for %s with %s', basename($definition->configFile), $definition->generatorClass));

        if (! class_exists($definition->generatorClass)) {
            $output->writeln(sprintf('Generator class %s does not exist', $definition->generatorClass));

            return;
        }

        $generatorClass = $definition->generatorClass;
        $generator = new $generatorClass($definition);
        $generator->createClassesForObjects($this->yamlReader->read($definition->configFile));
    }
}
