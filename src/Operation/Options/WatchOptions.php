<?php

namespace MongoDB\Operation\Options;

final class WatchOptions
{
    const BATCH_SIZE = 'batchSize';
    const COLLATION = 'collation';
    const FULL_DOCUMENT = 'fullDocument';
    const MAX_AWAIT_TIME_MS = 'maxAwaitTimeMS';
    const READ_CONCERN = 'readConcern';
    const READ_PREFERENCE = 'readPreference';
    const RESUME_AFTER = 'resumeAfter';
    const SESSION = 'session';
    const START_AFTER = 'startAfter';
    const START_AT_OPERATION_TIME = 'startAtOperationTime';
    const TYPE_MAP = 'typeMap';
}
