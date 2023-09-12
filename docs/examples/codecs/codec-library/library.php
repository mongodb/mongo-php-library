<?php

use MongoDB\Codec\CodecLibrary;

$codecLibrary = new CodecLibrary([
    new DateTimeCodec(),
]);

$personCodec = new PersonCodec($codecLibrary);
