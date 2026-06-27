<?php

echo "<pre>";

echo "MYSQLHOST = " . getenv('MYSQLHOST') . PHP_EOL;
echo "MYSQLPORT = " . getenv('MYSQLPORT') . PHP_EOL;
echo "MYSQLDATABASE = " . getenv('MYSQLDATABASE') . PHP_EOL;
echo "MYSQLUSER = " . getenv('MYSQLUSER') . PHP_EOL;

echo PHP_EOL;

echo "database.default.hostname = " . env('database.default.hostname') . PHP_EOL;
echo "database.default.port = " . env('database.default.port') . PHP_EOL;