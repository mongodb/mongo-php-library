<?php
namespace MongoDB\QueryFlags;

const TAILABLE_CURSOR   = 0x02;
const SLAVE_OKAY        = 0x04;
const OPLOG_REPLY       = 0x08;
const NO_CURSOR_TIMEOUT = 0x10;
const AWAIT_DATA        = 0x20;
const EXHAUST           = 0x40;
const PARTIAL           = 0x80;


