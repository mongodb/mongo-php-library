<?php

/*
 * Copyright 2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Options;

final class DriverOptions
{
    const ALLOW_INVALID_HOSTNAME = 'allow_invalid_hostname';
    const CERTIFICATE_AUTHORITY_DIR = 'ca_dir';
    const CERTIFICATE_AUTHORITY_FILE = 'ca_file';
    const CONTEXT = 'context';
    const CERTIFICATE_REVOCATION_LIST_FILE = 'crl_file';
    const PEM_FILE = 'pem_file';
    const PEM_PASSPHRASE = 'pem_pwd';
    const WEAK_CERT_VALIDATION = 'weak_cert_validation';
}
