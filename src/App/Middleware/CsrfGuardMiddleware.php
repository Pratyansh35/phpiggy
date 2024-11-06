<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class CsrfGuardMiddleware implements MiddlewareInterface
{
    /**
     * @throws \Exception
     */
    public function process(callable $next)
    {
        $request_method = strtoupper($_SERVER['REQUEST_METHOD']);
        $valid_methods = [
            'POST',
            'PATCH',
            'DELETE',
        ];

        if (!in_array($request_method, $valid_methods)) {
            $next();
            return;
        }

        if ($_SESSION['token'] !== $_POST['token']) {
            redirectTo('/');
        }

        unset($_SESSION['token']);

        $next();
    }
}
