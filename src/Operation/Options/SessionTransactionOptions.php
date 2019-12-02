<?php

namespace MongoDB\Operation\Options;

final class SessionTransactionOptions
{
    const MAX_COMMIT_TIME_MS = 'maxCommitTimeMS';
    const READ_CONCERN = 'readConcern';
    const READ_PREFERENCE = 'readPreference';
    const WRITE_CONCERN = 'writeConcern';
}
