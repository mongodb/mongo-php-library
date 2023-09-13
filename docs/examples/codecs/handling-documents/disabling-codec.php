<?php

// Returns a document using the default type map
$collection->findOne($filter, ['codec' => null]);

// Disables codec usage as the aggregate result will have a different format
$collection->aggregate($pipeline, ['codec' => null]);
