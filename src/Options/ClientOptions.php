<?php

namespace MongoDB\Options;

final class ClientOptions
{
    const ALLOW_INVALID_HOSTNAME = 'allow_invalid_hostname';
    const CERTIFICATE_AUTHORITY_DIR = 'ca_dir';
    const CERTIFICATE_AUTHORITY_FILE = 'ca_file';
    const CONTEXT = 'context';
    const CERTIFICATE_REVOCATION_LIST_FILE = 'crl_file';
    const PEM_FILE = 'pem_file';
    const PEM_PASSPHRASE = 'pem_pwd';
    const TYPE_MAP = 'typeMap';
    const WEAK_CERT_VALIDATION = 'weak_cert_validation';
}
