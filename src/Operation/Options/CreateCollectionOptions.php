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

final class CreateCollectionOptions
{
    const AUTO_INDEX_ID = 'autoIndexId';
    const CAPPED = 'capped';
    const COLLATION = 'collation';
    const FLAGS = 'flags';
    const INDEX_OPTION_DEFAULTS = 'indexOptionDefaults';
    const MAX = 'max';
    const MAX_TIME_MS = 'maxTimeMS';
    const SESSION = 'session';
    const SIZE = 'size';
    const STORAGE_ENGINE = 'storageEngine';
    const TYPE_MAP = 'typeMap';
    const VALIDATION_ACTION = 'validationAction';
    const VALIDATION_LEVEL = 'validationLevel';
    const VALIDATOR = 'validator';
    const WRITE_CONCERN = 'writeConcern';
}
