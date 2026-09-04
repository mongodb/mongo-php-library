<?php

/**
 * Resolves the version of the mongodb extension to install in CI.
 *
 * Everything is deduced from the "ext-mongodb" constraint in composer.json and
 * from the list of releases published on PECL. The output is made of KEY=VALUE
 * lines, to be evaluated by the shell:
 *
 *     eval "$(php tools/extension-version.php stable)"
 *
 * Usage: extension-version.php lowest|stable|next-stable|next-minor [composer.json]
 */

const ALL_RELEASES_URL = 'https://pecl.php.net/rest/r/mongodb/allreleases.xml';

function fail(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

/** @return array{0: int, 1: string} Major version and lowest version allowed by the constraint */
function parseConstraint(string $composerJson): array
{
    $contents = @file_get_contents($composerJson);

    if ($contents === false) {
        fail(sprintf('Cannot read %s', $composerJson));
    }

    try {
        $composer = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        fail(sprintf('Cannot parse %s: %s', $composerJson, $exception->getMessage()));
    }

    $constraint = $composer['require']['ext-mongodb'] ?? fail(sprintf('No ext-mongodb constraint in %s', $composerJson));

    if (! preg_match('/^\^(\d+)\.(\d+)(?:\.(\d+))?$/', $constraint, $matches)) {
        fail(sprintf('Unsupported ext-mongodb constraint: %s', $constraint));
    }

    return [(int) $matches[1], sprintf('%d.%d.%d', $matches[1], $matches[2], $matches[3] ?? 0)];
}

/**
 * Stable versions published on PECL that satisfy the constraint, sorted from
 * the most recent one. The document is parsed with a regular expression to
 * avoid depending on an XML extension.
 *
 * @return list<string>
 */
function releasedVersions(int $major, string $lowest): array
{
    $document = @file_get_contents(ALL_RELEASES_URL, false, stream_context_create(['http' => ['timeout' => 30]]));

    if ($document === false) {
        fail(sprintf('Cannot read the list of mongodb releases from %s', ALL_RELEASES_URL));
    }

    // Only version numbers are matched, so that the output of this script stays
    // safe to evaluate in a shell
    if (! preg_match_all('#<v>([\d.]+)</v>\s*<s>(\w+)</s>#', $document, $matches, PREG_SET_ORDER)) {
        fail(sprintf('No release found in %s', ALL_RELEASES_URL));
    }

    $versions = [];

    foreach ($matches as [, $version, $stability]) {
        if ($stability !== 'stable') {
            continue;
        }

        if (version_compare($version, $lowest, '>=') && version_compare($version, ($major + 1) . '.0.0', '<')) {
            $versions[] = $version;
        }
    }

    // The document lists the most recent release first, but sort explicitly
    // instead of relying on that order
    usort($versions, fn (string $a, string $b) => version_compare($b, $a));

    return $versions;
}

$targets = ['lowest', 'stable', 'next-stable', 'next-minor'];
$target = $argv[1] ?? '';
$composerJson = $argv[2] ?? dirname(__DIR__) . '/composer.json';

if (! in_array($target, $targets, true)) {
    fail(sprintf('Unknown target "%s", expected one of: %s', $target, implode(', ', $targets)));
}

[$major, $lowest] = parseConstraint($composerJson);
$released = releasedVersions($major, $lowest);

$version = '';
$branch = '';

if ($released === []) {
    // The required version is not released yet, so the extension can only be
    // compiled from the development branch of the next minor version.
    $branch = sprintf('v%d.x', $major);

    fwrite(STDERR, sprintf(
        'No version published on PECL satisfies the ext-mongodb constraint of %s, falling back to branch %s' . PHP_EOL,
        $composerJson,
        $branch,
    ));
} else {
    switch ($target) {
        case 'lowest':
            if (! in_array($lowest, $released, true)) {
                fail(sprintf('Version %s is not published on PECL', $lowest));
            }

            $version = $lowest;
            break;

        case 'stable':
            $version = $released[0];
            break;

        case 'next-stable':
            // Maintenance branch of the latest released minor version
            $branch = 'v' . implode('.', array_slice(explode('.', $released[0]), 0, 2));
            break;

        case 'next-minor':
            $branch = sprintf('v%d.x', $major);
            break;
    }
}

echo <<<EOL
EXTENSION_VERSION={$version}
EXTENSION_BRANCH={$branch}

EOL;
