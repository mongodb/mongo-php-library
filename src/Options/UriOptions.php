<?php

namespace MongoDB\Options;

final class UriOptions
{
    const APP_NAME = 'appname';
    const AUTH_MECHANISM = 'authMechanism';
    const AUTH_MECHANISM_PROPERTIES = 'authMechanismProperties';
    const AUTH_SOURCE = 'authSource';
    const CANONICALIZE_HOSTNAME = 'canonicalizeHostname';
    const COMPRESSORS = 'compressors';
    const CONNECT_TIMEOUT_MS = 'connectTimeoutMS';
    const GSSAPI_SERVICE_NAME = 'gssapiServiceName';
    const HEARTBEAT_FREQUENCY_MS = 'heartbeatFrequencyMS';
    const JOURNAL = 'journal';
    const LOCAL_THRESHOLD_MS = 'localThresholdMS';
    const MAX_STALENESS_SECONDS = 'maxStalenessSeconds';
    const PASSWORD = 'password';
    const READ_CONCERN_LEVEL = 'readConcernLevel';
    const READ_PREFERENCE = 'readPreference';
    const READ_PREFERENCE_TAGS = 'readPreferenceTags';
    const REPLICA_SET = 'replicaSet';
    const RETRY_READS = 'retryReads';
    const RETRY_WRITES = 'retryWrites';
    const SAFE = 'safe';
    const SERVER_SELECTION_TIMEOUT_MS = 'serverSelectionTimeoutMS';
    const SERVER_SELECTION_TRY_ONCE = 'serverSelectionTryOnce';
    const SLAVE_OK = 'slaveOk';
    const SOCKET_CHECK_INTERVAL_MS = 'socketCheckIntervalMS';
    const SOCKET_TIMEOUT_MS = 'socketTimeoutMS';
    const SSL = 'ssl';
    const TLS = 'tls';
    const TLS_ALLOW_INVALID_CERTIFICATES = 'tlsAllowInvalidCertificates';
    const TLS_ALLOW_INVALID_HOSTNAMES = 'tlsAllowInvalidHostnames';
    const TLS_CAFILE = 'tlsCAFile';
    const TLS_CERTIFICATE_KEY_FILE = 'tlsCertificateKeyFile';
    const TLS_CERTIFICATE_KEY_FILE_PASSWORD = 'tlsCertificateKeyFilePassword';
    const TLS_INSECURE = 'tlsInsecure';
    const USERNAME = 'username';
    const WRITE_CONCERN = 'w';
    const WRITE_CONCERN_TIMEOUT_MS = 'wTimeoutMS';
    const ZLIB_COMPRESSION_LEVEL = 'zlibCompressionLevel';
}
