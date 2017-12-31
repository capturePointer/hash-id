<?php

require(__DIR__ . '/../../../autoload.php');

use Yison\HashId;

$base = 64;
$hashId = new HashId($base);
$id = 100;
$encr = $hashId->encode($id);
$decr = $hashId->decode($encr);

printf('%d, %s, %d' . PHP_EOL, $id, $encr, $decr);
