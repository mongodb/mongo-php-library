<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Yaml\Yaml;

use function array_column;
use function array_combine;
use function array_key_exists;
use function array_shift;
use function assert;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function htmlspecialchars_decode;
use function ksort;
use function mkdir;
use function sort;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function strip_tags;
use function trim;
use function var_export;

use const PHP_EOL;

final class ScrapeCommand extends Command
{
    private Crawler $crawler;

    /** @var array<string, string> $tabs Associative array of names to table ids */
    private array $tabs;

    public function __construct(
        private string $configDir,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('scrape');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $index = file_get_contents('https://docs.google.com/spreadsheets/d/e/2PACX-1vROpGTJGXAKf2SVuSZaw16NwMVtzMVGH9b-YiMtddgZRZOjOO6jK2YLbTUZ0N_qe74nxGY9hYhUe-l2/pubhtml');
        $this->crawler = new Crawler($index);
        $this->extractTabs();

        $docs = $this->getTableData($this->crawler, 'aggregation pipeline operators');
        foreach ($docs as $doc) {
            $this->writeYamlFile('aggregation-operators', $this->formatSpec($doc));
        }

        $docs = $this->getTableData($this->crawler, 'query operators');
        foreach ($docs as $doc) {
            $this->writeYamlFile('query-operators', $this->formatSpec($doc));
        }

        $docs = $this->getTableData($this->crawler, 'aggregation pipeline stages');
        foreach ($docs as $doc) {
            $this->writeYamlFile('aggregation-stages', $this->formatSpec($doc));
        }

        return Command::SUCCESS;
    }

    private function extractTabs(): void
    {
        // Extract tab names and ids
        $tabs = $this->crawler->filter('#sheet-menu > li')->each(fn (Crawler $li) => [
            'name' => $li->text(),
            'id' => str_replace('sheet-button-', '', $li->attr('id')),
        ]);

        $this->tabs = array_combine(array_column($tabs, 'name'), array_column($tabs, 'id'));
    }

    private function getTableData(Crawler $crawler, string $tabName): array
    {
        $id = $this->tabs[$tabName] ?? throw new InvalidArgumentException('Invalid tab name: ' . $tabName);

        $table = $crawler->filter('#' . $id . ' table > tbody');

        // Load the table into a 2D array
        $rows = [];
        $table->filter('tr')->each(function (Crawler $row, $rowIndex) use (&$rows): void {
            $cellIndex = 0;

            $row->filter('td')->each(function (Crawler $cell) use (&$rows, &$rowIndex, &$cellIndex): bool {
                // Skip freezebar cells
                if (str_contains($cell->attr('class') ?? '', 'freezebar-cell')) {
                    return true;
                }

                $rowspan = $cell->attr('rowspan') ?: 1;

                // Advance to the next available cell
                while (array_key_exists($rowIndex, $rows) && array_key_exists($cellIndex, $rows[$rowIndex])) {
                    $cellIndex++;
                }

                $value = $cell->html();
                $value = str_replace(['<br>', '<br />', '<br/>'], PHP_EOL, $value);
                $value = strip_tags($value);
                $value = htmlspecialchars_decode($value);
                $value = trim($value);

                // Fill the next cells with null for colspan and rowspan
                for ($rowIndexLoop = $rowIndex; $rowIndexLoop < $rowIndex + $rowspan; $rowIndexLoop++) {
                    if ($rowIndexLoop === $rowIndex) {
                        $rows[$rowIndexLoop][$cellIndex] = $value;
                    } else {
                        $rows[$rowIndexLoop][$cellIndex] = null;
                    }
                }

                return false;
            });

            if (isset($rows[$rowIndex])) {
                ksort($rows[$rowIndex]);
            }
        });

        // Extract headers from first row
        $headers = array_shift($rows);

        // Map header to field names + aggregate args
        $docs = [];
        $docId = 0;
        $argId = 0;
        foreach ($rows as $row) {
            // Create a new document for each row that starts with a non-empty cell
            if ($row[0] && $docs !== []) {
                $docId++;
                $argId = 0;
            }

            foreach ($row as $index => $cell) {
                if (str_contains($headers[$index], 'Arg')) {
                    $docs[$docId]['Args'][$argId][str_replace('Arg', '', $headers[$index])] = $cell;
                } elseif (null !== $cell) {
                    $docs[$docId][$headers[$index]] = $cell;
                }
            }

            $argId++;
        }

        return $docs;
    }

    /**
     * @param array{
     *            Name: string,
     *            Category: string,
     *            Description: string,
     *            Link: string,
     *            ReturnType: string,
     *            Encode: string,
     *            Args: array{ Name: string, Type: string, Options: string, Description: string }
     *        } $doc
     */
    private function formatSpec(array $doc): array
    {
        foreach (['Name', 'Category', 'Description', 'Link', 'ReturnType', 'Encode', 'Args'] as $key) {
            assert(isset($doc[$key]), 'Missing ' . $key . ' for ' . var_export($doc, true));
        }

        $spec = [];
        $spec['name'] = $doc['Name'];
        $spec['category'] = explode(PHP_EOL, $doc['Category']);
        sort($spec['category']);
        $spec['link'] = $doc['Link'];
        $spec['returnType'] = explode(PHP_EOL, $doc['ReturnType']);
        $spec['encode'] = $doc['Encode'];

        if ($doc['Description']) {
            $spec['description'] = $doc['Description'] . PHP_EOL;
        }

        foreach ($doc['Args'] as $arg) {
            foreach (['Name', 'Type', 'Options', 'Description'] as $key) {
                assert(isset($arg[$key]), 'Missing Arg' . $key . ' for ' . var_export($doc, true));
            }

            $parameter = [];
            $parameter['name'] = $arg['Name'];
            $parameter['type'] = explode(PHP_EOL, $doc['ReturnType']);
            if (str_contains($arg['Options'], 'Optional')) {
                $parameter['optional'] = true;
            }

            if ($arg['Description']) {
                $parameter['description'] = $arg['Description'] . PHP_EOL;
            }

            $spec['parameters'][] = $parameter;
        }

        return $spec;
    }

    private function writeYamlFile(string $dirname, array $data): void
    {
        $yaml = Yaml::dump($data, 3, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $dirname = $this->configDir . '/' . $dirname;
        if (! file_exists($dirname)) {
            mkdir($dirname, 0755);
        }

        $name = str_replace('$', '', $data['name']) ?: 'positional';
        $filename = $dirname . '/' . $name . '.yaml';

        // Add a schema reference to the top of the file
        $schema = '# $schema: ../schema.json' . PHP_EOL;

        // Add a trailing newline if one is not present
        if (! str_ends_with($yaml, PHP_EOL)) {
            $yaml .= PHP_EOL;
        }

        file_put_contents($filename, $schema . $yaml);
    }
}
