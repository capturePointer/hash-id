<?php

require(__DIR__ . '/../../../autoload.php');

test(31);
test(36);
test(62);
test(64);

function test($base)
{
    printf('base %d, start:' . PHP_EOL, $base);
    $hashId = new Yison\HashId($base);

    $errorOccurred = false;

    $percent = 0;
    $loop = 0;

    $step = 1.00001;
    $testCaseRound = ceil(log(PHP_INT_MAX) / log($step));

    for ($i = 1; $i <= PHP_INT_MAX;) {
        $i = ceil($i * $step) < PHP_INT_MAX ? ceil($i * $step) : PHP_INT_MAX;
        $encr = $hashId->encode($i);
        $decr = $hashId->decode($encr);

        $loop++;
        if ($loop / $testCaseRound * 100 > $percent) {
            $percent++;
            printf('(Current Number -> Encode Text -> Decode Number) %d -> %s -> %d' . PHP_EOL, $i, $encr, $decr);
        }

        if ($i != $decr) {
            $errorOccurred = true;
            printf('ERROR: %d -> %s -> %d' . PHP_EOL, $i, $encr, $decr);
        }

        if ($i == PHP_INT_MAX) {
            break;
        }
    }

    if (!$errorOccurred) {
        printf('%s' . PHP_EOL, 'No error occurred.');
    }
    printf('test case count: %d, coverage: %.20f%%, done.' . PHP_EOL, $loop, ($loop / PHP_INT_MAX * 100));
}