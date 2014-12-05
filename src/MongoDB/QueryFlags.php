<?php
namespace MongoDB\QueryFlags;

const TAILABLE_CURSOR   = 1 << 1;
const SLAVE_OKAY        = 1 << 2;
const OPLOG_REPLY       = 1 << 3;
const NO_CURSOR_TIMEOUT = 1 << 4;
const AWAIT_DATA        = 1 << 5;
const EXHAUST           = 1 << 6;
const PARTIAL           = 1 << 7;


