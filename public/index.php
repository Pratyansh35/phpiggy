<?php

include __DIR__ . "/../src/App/functions.php";
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$app = include __DIR__ . "/../src/App/bootstrap.php";

$app->run();
