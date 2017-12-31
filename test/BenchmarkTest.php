<?php

require(__DIR__ . '/../../../autoload.php');

testBundle(31);
testBundle(36);
testBundle(62);
testBundle(64);


function testBundle($base)
{
    printf('base %d, start:' . PHP_EOL, $base);

    $hashId = new Yison\HashId($base);
    $count = 1000000;

    // [0, $count]
    $startAt = microtime(1);

    $encr = [];
    for ($i = 0; $i <= $count; $i++) {
        $encr[] = $hashId->encode($i);
    }

    $used = microtime(1) - $startAt;
    $speed = $count / $used;
    printf('[0, %d] encode: count: %d, used: %.2fs, %d/s' . PHP_EOL, $count, $count, $used, $speed);

    $startAt = microtime(1);
    foreach ($encr as $e) {
        $decr = $hashId->decode($e);
    }

    $used = microtime(1) - $startAt;
    $speed = $count / $used;
    printf('[0, %d] decode count: %d, used: %.2fs, %d/s' . PHP_EOL, $count, $count, $used, $speed);
    printf('last one: number -> encode -> decode: %d -> %s -> %d' . PHP_EOL . PHP_EOL, $i - 1, array_pop($encr), $decr);

    $startAt = microtime(1);

    // [PHP_INT_MAX / 2, PHP_INT_MAX / 2 + $count]
    $encr = [];
    $i = intdiv(PHP_INT_MAX, 2);
    $end = $i + $count;
    for ($i; $i <= $end; $i++) {
        $encr[] = $hashId->encode($i);
    }

    $used = microtime(1) - $startAt;
    $speed = $count / $used;
    printf('[%d, %d] encode: count: %d, used: %.2f, %d/s' . PHP_EOL, intdiv(PHP_INT_MAX, 2), intdiv(PHP_INT_MAX, 2) + $count, $count, $used, $speed);

    $startAt = microtime(1);

    foreach ($encr as $e) {
        $decr = $hashId->decode($e);
    }

    $used = microtime(1) - $startAt;
    $speed = $count / $used;
    printf('[%d, %d] decode: count: %d, used: %.2f, %d/s' . PHP_EOL, intdiv(PHP_INT_MAX, 2), intdiv(PHP_INT_MAX, 2) + $count, $count, $used, $speed);
    printf('last one: number -> encode -> decode: %d -> %s -> %d' . PHP_EOL . PHP_EOL, $i - 1, array_pop($encr), $decr);

    $startAt = microtime(1);

    // [PHP_INT_MAX - $count, PHP_INT_MAX]
    $encr = [];
    $i = PHP_INT_MAX - $count;
    for ($i; $i < PHP_INT_MAX; $i++) {
        $encr[] = $hashId->encode($i);
    }

    $used = microtime(1) - $startAt;
    $speed = $count / $used;
    printf('[%d, %d] encode: count: %d, used: %.2f, %d/s' . PHP_EOL, intval(PHP_INT_MAX - $count), PHP_INT_MAX, $count, $used, $speed);

    $startAt = microtime(1);

    foreach ($encr as $e) {
        $decr = $hashId->decode($e);
    }
    $used = microtime(1) - $startAt;
    $speed = $count / $used;
    printf('[%d, %d] decode: count: %d, used: %.2f, %d/s' . PHP_EOL, intval(PHP_INT_MAX - $count), PHP_INT_MAX, $count, $used, $speed);
    printf('last one: number -> encode -> decode: %d -> %s -> %d' . PHP_EOL . PHP_EOL . PHP_EOL, $i - 1, array_pop($encr), $decr);
}