<?php

namespace MongoDB\CodeGenerator\Command;

use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\YamlReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'generate', description: 'Generate code for mongodb/mongodb library')]
final class GenerateCommand extends Command
{
    public function __construct(
        private string $configFile,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addOption('force', 'f', null, 'Force generation of all files');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating code for mongodb/mongodb library');

        $yamlReader = new YamlReader();
        $config = require $this->configFile;

        // @todo This is a hack to get the first pipeline operator config
        $config = $config['pipeline-operators'][0];

        $config = new GeneratorDefinition($config);
        $generatorClass = $config->generatorClass;
        $generator = new $generatorClass($config);
        $generator->createClassesForObjects($yamlReader->read($config->configFile));

        return Command::SUCCESS;
    }
}
