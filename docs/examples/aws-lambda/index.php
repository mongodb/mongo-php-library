<?php

use MongoDB\Client;

require_once __DIR__ . '/vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: throw new RuntimeException('The MONGODB_URI environment variable is not set');
$client = new Client($uri);
$planets = $client->sample_guides->planets
    ->find(
        [],
        ['sort' => ['orderFromSun' => 1]],
    );

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MongoDB Planets</title>
</head>
<body>
    <ul>
        <?php foreach ($planets as $planet) : ?>
            <li><?= $planet->name ?></li>
        <?php endforeach ?>
    </ul>
</body>
</html>
