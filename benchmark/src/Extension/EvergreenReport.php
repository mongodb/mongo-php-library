<?php

namespace MongoDB\Benchmark\Extension;

use PhpBench\Compat\SymfonyOptionsResolverCompat;
use PhpBench\Model\Suite;
use PhpBench\Model\SuiteCollection;
use PhpBench\Path\Path;
use PhpBench\Registry\Config;
use PhpBench\Report\GeneratorInterface;
use PhpBench\Report\Model\Reports;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function assert;
use function date;
use function dirname;
use function file_exists;
use function file_put_contents;
use function json_encode;
use function mkdir;
use function sprintf;

use const DATE_ATOM;
use const JSON_PRETTY_PRINT;

class EvergreenReport implements GeneratorInterface
{
    private const PARAM_PATH = 'path';

    public function __construct(private string $cwd)
    {
    }

    public function configure(OptionsResolver $options): void
    {
        $options->setDefaults([self::PARAM_PATH => '.phpbench/results.json']);
        $options->setAllowedTypes(self::PARAM_PATH, ['string']);

        SymfonyOptionsResolverCompat::setInfos($options, [self::PARAM_PATH => 'Path to output file']);
    }

    public function generate(SuiteCollection $collection, Config $config): Reports
    {
        $tests = [];

        foreach ($collection as $suite) {
            assert($suite instanceof Suite);
            foreach ($suite as $benchmark) {
                foreach ($benchmark as $subject) {
                    foreach ($subject->getVariants() as $variant) {
                        $stats = $variant->getStats()->getStats();
                        $name = sprintf('%s::%s', $benchmark->getName(), $subject->getName());
                        if ($variant->getParameterSet()->getName()) {
                            $name .= '#' . $variant->getParameterSet()->getName();
                        }

                        $tests[] = [
                            'info' => ['test_name' => $name],
                            'created_at' => date(DATE_ATOM),
                            'completed_at' => date(DATE_ATOM),
                            'metrics' => [
                                [
                                    'name' => 'Avg. Time',
                                    'type' => 'MEAN',
                                    'value' => $stats['mean'],
                                ],
                                [
                                    'name' => 'Min. Time',
                                    'type' => 'MIN',
                                    'value' => $stats['min'],
                                ],
                                [
                                    'name' => 'Max. Time',
                                    'type' => 'MAX',
                                    'value' => $stats['max'],
                                ],
                                [
                                    'name' => 'Std. Deviation',
                                    'type' => 'STANDARD_DEVIATION',
                                    'value' => $stats['stdev'],
                                ],
                            ],
                        ];
                    }
                }
            }
        }

        $outputPath = Path::makeAbsolute($config[self::PARAM_PATH], $this->cwd);
        $outputDir = dirname($outputPath);

        if (! file_exists($outputDir)) {
            if (! @mkdir($outputDir, 0777, true)) {
                throw new RuntimeException(sprintf(
                    'Could not create directory "%s"',
                    $outputDir,
                ));
            }
        }

        if (false === file_put_contents($outputPath, json_encode($tests, JSON_PRETTY_PRINT) . "\n")) {
            throw new RuntimeException(sprintf(
                'Could not write report to file "%s"',
                $outputPath,
            ));
        }

        // Return an empty report to not confuse the report renderer
        return Reports::empty();
    }
}
