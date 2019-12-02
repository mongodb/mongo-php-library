<?php

namespace MongoDB\Operation\Options;

final class CreateIndexOptions
{
    const BACKGROUND = 'background';
    const COLLATION = 'collation';
    const EXPIRE_AFTER_SECONDS = 'expireAfterSeconds';
    const MAX_TIME_MS = 'maxTimeMS';
    const NAME = 'name';
    const PARTIAL_FILTER_EXPRESSION = 'partialFilterExpression';
    const SESSION = 'session';
    const SPARSE = 'sparse';
    const TWO_D_SPHERE_INDEX_VERSION = '2dsphereIndexVersion';
    const UNIQUE = 'unique';
    const WRITE_CONCERN = 'writeConcern';
}
