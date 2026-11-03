<?php

function getHosts(string $uri): array
{
    if (! str_contains($uri, '://')) {
        return [$uri];
    }

    $parsed = parse_url($uri);

    if (! isset($parsed['scheme'])) {
        throw new RuntimeException('URI must contain a scheme');
    }

    if ($parsed['scheme'] === 'mongodb+srv') {
        return getHostsFromSrv($uri);
    }

    if ($parsed['scheme'] !== 'mongodb') {
        throw new RuntimeException('Unsupported scheme: ' . $parsed['scheme']);
    }

    $hosts = sprintf('%s:%d', $parsed['host'], $parsed['port'] ?? 27017);

    return explode(',', $hosts);
}

/**
 * Resolves SRV records for a MongoDB+SRV URI according to the specification:
 * https://github.com/mongodb/specifications/blob/master/source/initial-dns-seedlist-discovery/initial-dns-seedlist-discovery.md
 *
 * @return list<string> List of host:port strings
 */
function getHostsFromSrv(string $uri): array
{
    $parsed = parse_url($uri);
    if (! isset($parsed['host'])) {
        throw new RuntimeException('SRV URI must contain a host');
    }

    $srvRecord = '_mongodb._tcp.' . $parsed['host'];

    $records = dns_get_record($srvRecord, DNS_SRV);
    if ($records === false || count($records) === 0) {
        throw new RuntimeException('No SRV records found for ' . $srvRecord);
    }

    return array_map(
        static fn (array $record) => $record['target'] . ':' . ($record['port'] ?? 27017),
        $records,
    );
}

/** @param resource $stream */
function streamWrite($stream, string $data): int
{
    for ($written = 0; $written < strlen($data); $written += $fwrite) {
        $fwrite = fwrite($stream, substr($data, $written));

        if ($fwrite === false) {
            return $written;
        }
    }

    return $written;
}

/** @param resource $stream */
function streamRead($stream, int $length): string
{
    $contents = '';

    while (! feof($stream) && strlen($contents) < $length) {
        $fread = fread($stream, min($length - strlen($contents), 8192));

        if ($fread === false) {
            return $contents;
        }

        $contents .= $fread;
    }

    return $contents;
}

function connect(string $host, bool $ssl): void
{
    $uri = sprintf('%s://%s', $ssl ? 'ssl' : 'tcp', $host);
    $context = stream_context_create($ssl ? ['ssl' => ['capture_peer_cert' => true]] : []);
    $client = @stream_socket_client($uri, $errno, $errorMessage, 5, STREAM_CLIENT_CONNECT, $context);

    if ($client === false) {
        printf("Could not connect to %s: %s\n", $host, $errorMessage);

        return;
    }

    if ($ssl) {
        $peerCertificate = stream_context_get_params($client)['options']['ssl']['peer_certificate'] ?? null;

        if (! isset($peerCertificate)) {
            printf("Could not capture peer certificate for %s\n", $host);

            return;
        }

        $certificateProperties = openssl_x509_parse($peerCertificate);

        // TODO: Check that the certificate common name (CN) matches the hostname
        $now = new DateTime();
        $validFrom = DateTime::createFromFormat('U', $certificateProperties['validFrom_time_t']);
        $validTo = DateTime::createFromFormat('U', $certificateProperties['validTo_time_t']);
        $isValid = $now >= $validFrom && $now <= $validTo;

        printf("Peer certificate for %s is %s\n", $host, $isValid ? 'valid' : 'expired');

        if (! $isValid) {
            printf("  Valid from %s to %s\n", $validFrom->format('c'), $validTo->format('c'));
        }
    }

    $request = pack(
        'Va*xVVa*',
        1 << 2 /* slaveOk */,
        'admin.$cmd', /* namespace */
        0, /* numberToSkip */
        1, /* numberToReturn */
        hex2bin('130000001069734d6173746572000100000000'), /* { "isMaster": 1 } */
    );
    $requestLength = 16 /* MsgHeader length */ + strlen($request);
    $header = pack('V4', $requestLength, 0 /* requestID */, 0 /* responseTo */, 2004 /* OP_QUERY */);

    if ($requestLength !== streamWrite($client, $header . $request)) {
        printf("Could not write request to %s\n", $host);

        return;
    }

    $data = streamRead($client, 4);

    if ($data === false || strlen($data) !== 4) {
        printf("Could not read response header from %s\n", $host);

        return;
    }

    [, $responseLength] = unpack('V', $data);

    $data = streamRead($client, $responseLength - 4);

    if ($data === false || strlen($data) !== $responseLength - 4) {
        printf("Could not read response from %s\n", $host);

        return;
    }

    printf("Successfully received response from %s\n", $host);
}

$uri = $argv[1] ?? 'mongodb://127.0.0.1';
printf("Looking up MongoDB at %s\n", $uri);
$hosts = getHosts($uri);

if (str_starts_with($uri, 'mongodb+srv://')) {
    // For mongodb+srv, SSL is always true unless explicitly set to false
    $ssl = stripos(parse_url($uri, PHP_URL_QUERY) ?? '', 'ssl=false') === false;
} else {
    // For other connection strings, SSL is true if indicated
    $ssl = stripos(parse_url($uri, PHP_URL_QUERY) ?? '', 'ssl=true') !== false;
}

printf("Found %d host(s) in the URI. Will attempt to connect to each.\n", count($hosts));

foreach ($hosts as $host) {
    echo "\n";
    connect($host, $ssl);
}
