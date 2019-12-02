<?php

namespace MongoDB\Operation\Options;

final class BulkWriteOptions
{
    const BYPASS_DOCUMENT_VALIDATION = 'bypassDocumentValidation';
    const ORDERED = 'ordered';
    const SESSION = 'session';
    const WRITE_CONCERN = 'writeConcern';
}
