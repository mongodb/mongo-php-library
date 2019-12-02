<?php

namespace MongoDB\Operation\Options;

final class AggregateOptions
{
    const ALLOW_DISK_USE = 'allowDiskUse';
    const BATCH_SIZE = 'batchSize';
    const BYPASS_DOCUMENT_VALIDATION = 'bypassDocumentValidation';
    const COMMENT = 'comment';
    const EXPLAIN = 'explain';
    const HINT = 'hint';
    const MAX_TIME_MS = 'maxTimeMS';
    const READ_CONCERN = 'readConcern';
    const READ_PREFERENCE = 'readPreference';
    const SESSION = 'session';
    const TYPE_MAP = 'typeMap';
    const USE_CURSOR = 'useCursor';
    const WRITE_CONCERN = 'writeConcern';
}
