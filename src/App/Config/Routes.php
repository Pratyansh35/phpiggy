<?php

declare(strict_types=1);

namespace App\Config;

use App\Controllers\AboutController;
use App\Controllers\AuthController;
use App\Controllers\ErrorController;
use App\Controllers\HomeController;
use App\Controllers\ReceiptController;
use App\Controllers\TransactionController;
use App\Middleware\AuthRequiredMiddleware;
use App\Middleware\GuestOnlyMiddleware;
use Framework\App;

function registerRoutes(App $app): void
{
    $app->get('/', [HomeController::class, 'home'])->middleware(AuthRequiredMiddleware::class);
    $app->get('/about', [AboutController::class, 'about']);
    $app->get('/transaction', [TransactionController::class, 'createView'])->middleware(AuthRequiredMiddleware::class);
    $app->get('/register', [AuthController::class, 'registerForm'])->middleware(GuestOnlyMiddleware::class);
    $app->get('/login', [AuthController::class, 'loginForm'])->middleware(GuestOnlyMiddleware::class);
    $app->get('/logout', [AuthController::class, 'logout'])->middleware(AuthRequiredMiddleware::class);
    $app->post('/register', [AuthController::class, 'registerStore'])->middleware(GuestOnlyMiddleware::class);
    $app->post('/login', [AuthController::class, 'loginStore'])->middleware(GuestOnlyMiddleware::class);
    $app->post('/transaction', [TransactionController::class, 'createStore'])->middleware(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}', [TransactionController::class, 'editForm'])->middleware(AuthRequiredMiddleware::class);
    $app->post('/transaction/{transaction}', [TransactionController::class, 'editStore'])->middleware(AuthRequiredMiddleware::class);
    $app->delete('/transaction/{transaction}', [TransactionController::class, 'delete'])->middleware(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}/receipt', [ReceiptController::class, 'uploadView'])->middleware(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}/receipt/{receipt}', [ReceiptController::class, 'download'])->middleware(AuthRequiredMiddleware::class);
    $app->post('/transaction/{transaction}/receipt', [ReceiptController::class, 'upload'])->middleware(AuthRequiredMiddleware::class);
    $app->delete('/transaction/{transaction}/receipt/{receipt}', [ReceiptController::class, 'delete'])->middleware(AuthRequiredMiddleware::class);

    $app->setErrorHandler([ErrorController::class, 'notFound']);
}
