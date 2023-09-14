<?php

// Overrides the collection codec, falling back to the default type map
$collection->aggregate($pipeline, ['codec' => null]);

// Overrides the collection codec, using the specified type map
$collection->findOne($filter, ['typeMap' => ['root' => 'stdClass']]);
