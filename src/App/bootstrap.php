<?php

declare(strict_types = 1);

require __DIR__ . "/../../vendor/autoload.php";

use App\Config\Paths;
use Framework\App;
use Dotenv\Dotenv;
use function App\Config\{registerRoutes, registerMiddleware};

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);



$app = new App(Paths::SOURCE . "app/container-definitions.php");

registerRoutes($app);
registerMiddleware($app);

return $app;
