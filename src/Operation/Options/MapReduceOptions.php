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

namespace MongoDB\Operation\Options;

final class MapReduceOptions
{
    const BYPASS_DOCUMENT_VALIDATION = 'bypassDocumentValidation';
    const COLLATION = 'collation';
    const FINALIZE = 'finalize';
    const JS_MODE = 'jsMode';
    const LIMIT = 'limit';
    const MAX_TIME_MS = 'maxTimeMS';
    const QUERY = 'query';
    const READ_CONCERN = 'readConcern';
    const READ_PREFERENCE = 'readPreference';
    const SCOPE = 'scope';
    const SESSION = 'session';
    const SORT = 'sort';
    const TYPE_MAP = 'typeMap';
    const VERBOSE = 'verbose';
    const WRITE_CONCERN = 'writeConcern';
}
