#!/bin/env php
<?php

// Supported PHP versions. Add new versions to the beginning of the list
$supportedPhpVersions = [
    '8.3',
    '8.2',
    '8.1',
    '8.0',
    '7.4'
];

$latestPhpVersion = max($supportedPhpVersions);
$lowestPhpVersion = min($supportedPhpVersions);

// Supported MongoDB versions. Add new versions after "rapid"
$supportedMongoDBVersions = [
    'latest',
    'rapid',
    '7.0',
    '6.0',
    '5.0',
    '4.4',
    '4.2',
    '4.0',
    '3.6',
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
$allFiles[] = generateConfigs('build', 'phpVersion', 'build-extension.yml', 'build-php-%s', $supportedPhpVersions);

// Test tasks
$allFiles[] = generateConfigs('test', 'mongodbVersion', 'local.yml', 'local-%s', $localServerVersions);
$allFiles[] = generateConfigs('test', 'mongodbVersion', 'load-balanced.yml', 'load-balanced-%s', $loadBalancedServerVersions);
$allFiles[] = generateConfigs('test', 'mongodbVersion', 'require-api-version.yml', 'require-api-version-%s', $requireApiServerVersions);
$allFiles[] = generateConfigs('test', 'mongodbVersion', 'csfle.yml', 'csfle-%s', $csfleServerVersions);

// Test variants
$allFiles[] = generateConfigs('test-variant', 'phpVersion', 'latest.yml', 'latest-php-%s', [$latestPhpVersion]);
$allFiles[] = generateConfigs('test-variant', 'phpVersion', 'replicaset-only.yml', 'replicaset-php-%s', array_diff($supportedPhpVersions, [$latestPhpVersion]));
$allFiles[] = generateConfigs('test-variant', 'phpVersion', 'lowest.yml', 'lowest-php-%s', [$lowestPhpVersion]);

echo "Generated config. Use the following list to import files:\n";
echo implode("\n", array_map('getImportConfig', array_merge(...$allFiles))) . "\n";

function getImportConfig(string $filename): string
{
    return '- filename: ' . $filename;
}

function generateConfigs(
    string $directory,
    string $replacementName,
    string $templateFile,
    string $outputFormat,
    array $versions,
): array {
    $templateRelativePath = 'templates/' . $directory . '/' . $templateFile;
    $template = file_get_contents(__DIR__ . '/' . $templateRelativePath);
    $header = sprintf(
        '# This file is generated automatically - please edit the "%s" template file instead.',
        $templateRelativePath
    );

    $files = [];

    foreach ($versions as $version) {
        $filename = sprintf('/generated/%s/' . $outputFormat . '.yml', $directory, $version);
        $files[] = '.evergreen/config' . $filename;

        $replacements = ['%' . $replacementName . '%' => $version];

        file_put_contents(__DIR__ . $filename, $header . "\n" . strtr($template, $replacements));
    }

    return $files;
}
