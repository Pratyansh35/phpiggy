<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{

    public function process(callable $next)
    {
        try {
            $next();
        } catch (ValidationException $e) {
            $old_form_data = $_POST;
            $excluded_fields = ['password', 'confirm'];
            $formatted_form_data = array_diff_key(
                $old_form_data,
                array_flip($excluded_fields)
            );

            $_SESSION['errors'] = $e->errors;
            $_SESSION['old_form_data'] = $formatted_form_data;

            $referer = $_SERVER['HTTP_REFERER'];
            redirectTo($referer);
        }
    }
}
