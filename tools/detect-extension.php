<?php

function grepIniFile(string $filename, string $extension): int
{
    $lines = [];

    foreach (new SplFileObject($filename) as $i => $line) {
        if (strpos($line, 'extension') === false) {
            continue;
        }

        if (strpos($line, $extension) === false) {
            continue;
        }

        $lines[$i] = $line;
    }

    if (empty($lines)) {
        printf("No interesting lines in %s.\n", $filename);

        return 0;
    }

    printf("Interesting lines in %s...\n", $filename);
    foreach ($lines as $i => $line) {
        printf("  %d: %s\n", $i + 1, trim($line));
    }

    return count($lines);
}

$extension = $argv[1] ?? 'mongodb';
$extensionDir = ini_get('extension_dir');

$version = phpversion($extension);

if ($version !== false) {
    printf("Extension \"%s\" is loaded. Version: %s\n", $extension, $version);
    exit;
}

printf("Extension \"%s\" is not loaded. Will attempt to scan INI files.\n", $extension);

// Check main INI file
$ini = php_ini_loaded_file();
$lines = 0;

if ($ini === false) {
    printf("No php.ini file is loaded. Will attempt to scan additional INI files.\n");
} else {
    $lines += grepIniFile($ini, $extension);
}

// Check additional INI files in scan directory
// See: https://www.php.net/manual/en/configuration.file.php#configuration.file.scan
$files = php_ini_scanned_files();

if (empty($files)) {
    printf("No additional INI files are loaded. Nothing left to scan.\n");
} else {
    foreach (explode(',', $files) as $ini) {
        $lines += grepIniFile(trim($ini), $extension);
    }
}

$mask = defined('PHP_WINDOWS_VERSION_BUILD') ? 'php_%s.dll' : '%s.so';
$filename = sprintf($mask, $extension);
$extensionFileExists = file_exists($extensionDir . '/' . $filename);

echo "\n";
printf("PHP will look for extensions in: %s\n", $extensionDir);
printf("Checking if that directory is readable: %s\n", is_dir($extensionDir) || ! is_readable($extensionDir) ? 'yes' : 'no');
printf("Checking if extension file exists in that directory: %s\n", $extensionFileExists ? 'yes' : 'no');
echo "\n";

if ($extensionFileExists) {
    printf("A file named %s exists in the extension directory. Make sure you have enabled the extension in php.ini.\n", $filename);
} elseif (! defined('PHP_WINDOWS_VERSION_BUILD')) {
    // Installation instructions for non-Windows systems are only necessary if the extension file does not exist.
    printf("You should install the extension using the pecl command in %s\n", PHP_BINDIR);
    printf("After installing the extension, you should add \"extension=%s\" to php.ini\n", $filename);
}

if (defined('PHP_WINDOWS_VERSION_BUILD')) {
    $zts = PHP_ZTS ? 'Thread Safe (TS)' : 'Non Thread Safe (NTS)';
    $arch = PHP_INT_SIZE === 8 ? 'x64' : 'x86';
    $dll = sprintf("%d.%d %s %s", PHP_MAJOR_VERSION, PHP_MINOR_VERSION, $zts, $arch);

    printf("You likely need to download a Windows DLL for: %s\n", $dll);
    printf("Windows DLLs should be available from: https://pecl.php.net/package/%s\n", $extension);

    if ($extensionFileExists) {
        echo "If you have enabled the extension in php.ini and it is not loading, make sure you have downloaded the correct DLL file as indicated above.\n";
    } else {
        printf("After installing the extension, you should add \"extension=%s\" to php.ini\n", $filename);
    }
}
