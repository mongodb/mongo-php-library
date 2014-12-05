<?php
namespace MongoDB\CursorType;

const NON_TAILABLE   = 0 << 0;
const TAILABLE       = \MongoDB\QueryFlags\TAILABLE_CURSOR;
const TAILABLE_AWAIT = \MongoDB\QueryFlags\TAILABLE_CURSOR | \MongoDB\QueryFlags\AWAIT_DATA;


