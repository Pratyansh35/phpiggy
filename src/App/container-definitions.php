<?php

declare(strict_types=1);

use App\Config\Paths;
use Framework\Container;
use Framework\TemplateEngine;
use Framework\Database;
use App\Services\{
    TransactionService,
    UserService,
    ValidatorService,
    ReceiptService
};

return [
    ValidatorService::class => fn () => new ValidatorService(),
    TemplateEngine::class => fn () => new TemplateEngine(Paths::VIEW),
    Database::class => fn () => new Database('mysql', [
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'phpiggy',
], 'root', ''),
    UserService::class => fn(Container $container) => new UserService($container->get(Database::class)),
    TransactionService::class => fn(Container $container) => new TransactionService($container->get(Database::class)),
    ReceiptService::class => fn(Container $container) => new ReceiptService($container->get(Database::class))
];
