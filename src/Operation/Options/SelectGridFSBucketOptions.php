<?php

namespace MongoDB\Operation\Options;

final class SelectGridFSBucketOptions
{
    const BUCKET_NAME = 'bucketName';
    const CHUNK_SIZE_BYTES = 'chunkSizeBytes';
    const DISABLE_MD5 = 'disableMD5';
    const READ_CONCERN = 'readConcern';
    const READ_PREFERENCE = 'readPreference';
    const TYPE_MAP = 'typeMap';
    const WRITE_CONCERN = 'writeConcern';
}
