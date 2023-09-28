#!/bin/env php
<?php

// Supported PHP versions. Add new versions to the beginning of the list
$supportedPhpVersions = ['8.2', '8.1', '8.0', '7.4'];

// Supported MongoDB versions. Add new versions after "rapid"
$supportedMongoDBVersions = [
    'latest', 'rapid',
    '7.0', '6.0', '5.0',
    '4.4', '4.2', '4.0',
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
$allFiles[] = generateConfigs('build', 'phpVersion', '_template-build-extension.yml', 'build-php-%s', $supportedPhpVersions);

// Test tasks
$allFiles[] = generateConfigs('test', 'mongodbVersion', '_template-local.yml', 'local-%s', $localServerVersions);
$allFiles[] = generateConfigs('test', 'mongodbVersion', '_template-load-balanced.yml', 'load-balanced-%s', $loadBalancedServerVersions);
$allFiles[] = generateConfigs('test', 'mongodbVersion', '_template-require-api-version.yml', 'require-api-version-%s', $requireApiServerVersions);
$allFiles[] = generateConfigs('test', 'mongodbVersion', '_template-csfle.yml', 'csfle-%s', $csfleServerVersions);

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
    $template = file_get_contents(__DIR__ . '/' . $directory . '/' . $templateFile);
    $header = '# This file is generated automatically - please edit the corresponding template file!';

    $files = [];

    foreach ($versions as $version) {
        $filename = sprintf('/%s/' . $outputFormat . '.yml', $directory, $version);
        $files[] = '.evergreen/config' . $filename;

        $replacements = ['%' . $replacementName . '%' => $version];

        file_put_contents(__DIR__ . $filename, $header . "\n" . strtr($template, $replacements));
    }

    return $files;
}

