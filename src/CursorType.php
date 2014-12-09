<?php
namespace MongoDB\CursorType;

const NON_TAILABLE   = 0x00;

//const TAILABLE       = \MongoDB\QueryFlags\TAILABLE_CURSOR;
const TAILABLE       = 0x02;

//const TAILABLE_AWAIT = \MongoDB\QueryFlags\TAILABLE_CURSOR | \MongoDB\QueryFlags\AWAIT_DATA;
const TAILABLE_AWAIT = 0x22;


