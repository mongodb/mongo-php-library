#!/usr/bin/env php
<?php

// Supported PHP versions. Add new versions to the beginning of the list
$supportedPhpVersions = [
    '8.4',
    '8.3',
    '8.2',
    '8.1',
];

$latestPhpVersion = max($supportedPhpVersions);
$lowestPhpVersion = min($supportedPhpVersions);

// Supported MongoDB versions. Add new versions after "rapid"
$supportedMongoDBVersions = [
    'latest',
    'rapid',
    '8.0',
    '7.0',
    '6.0',
    '5.0',
    '4.4',
    '4.2',
];

// Server versions
$localServerVersions = $supportedMongoDBVersions;
$loadBalancedServerVersions = array_filter(
    $supportedMongoDBVersions,
    // Load balanced supports MongoDB 5.0+
    fn (string $version): bool => in_array($version, ['latest', 'rapid']) || version_compare($version, '5.0', '>='),
);
$requireApiServerVersions = array_filter(
    $supportedMongoDBVersions,
    // requireApiVersion supports MongoDB 5.0+
    fn (string $version): bool => in_array($version, ['latest', 'rapid']) || version_compare($version, '5.0', '>='),
);
$csfleServerVersions = array_filter(
    $supportedMongoDBVersions,
    // Test CSFLE on MongoDB 4.2+
    fn (string $version): bool => in_array($version, ['latest', 'rapid']) || version_compare($version, '4.2', '>='),
);

$allFiles = [];

// Build tasks
$allFiles[] = generateConfigs('tasks', 'build', 'phpVersion', 'build-extension.yml', $supportedPhpVersions);

// Test tasks
$allFiles[] = generateConfigs('tasks', 'test', 'mongodbVersion', 'local.yml', $localServerVersions);
$allFiles[] = generateConfigs('tasks', 'test', 'mongodbVersion', 'load-balanced.yml', $loadBalancedServerVersions);
$allFiles[] = generateConfigs('tasks', 'test', 'mongodbVersion', 'require-api-version.yml', $requireApiServerVersions);
$allFiles[] = generateConfigs('tasks', 'test', 'mongodbVersion', 'csfle.yml', $csfleServerVersions);

// Test variants
$allFiles[] = generateConfigs('buildvariants', 'test-variant', 'phpVersion', 'modern-php-full.yml', $supportedPhpVersions);
$allFiles[] = generateConfigs('buildvariants', 'test-variant', 'phpVersion', 'phpc.yml', [$latestPhpVersion]);
$allFiles[] = generateConfigs('buildvariants', 'test-variant', 'phpVersion', 'lowest.yml', [$lowestPhpVersion]);

echo "Generated config. Use the following list to import files:\n";
echo implode("\n", array_map('getImportConfig', $allFiles)) . "\n";

function getImportConfig(string $filename): string
{
    return '- filename: ' . $filename;
}

function generateConfigs(
    string $type,
    string $directory,
    string $replacementName,
    string $templateFile,
    array $versions,
): string {
    $templateRelativePath = 'templates/' . $directory . '/' . $templateFile;
    $template = file_get_contents(__DIR__ . '/' . $templateRelativePath);
    $header = sprintf(
        '# This file is generated automatically - please edit the "%s" template file instead.',
        $templateRelativePath
    );

    $contents = <<<HEADER
$header
$type:

HEADER;

    foreach ($versions as $version) {
        $contents .= strtr(
            $template,
            ['%' . $replacementName . '%' => $version],
        );
    }

    $filename = '/generated/' . $directory . '/' . $templateFile;
    file_put_contents(__DIR__ . $filename, $contents);

    return '.evergreen/config' . $filename;
}
