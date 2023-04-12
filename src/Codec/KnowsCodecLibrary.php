<?php

namespace MongoDB\Codec;

interface KnowsCodecLibrary
{
    public function attachLibrary(CodecLibrary $library): void;
}
