<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class DateFormatRule implements RuleInterface
{

    public function validate(array $data, string $field, array $params): bool
    {
        $parse_date = date_parse_from_format($params[0], $data[$field]);

        return $parse_date['error_count'] === 0 && $parse_date['warning_count'] === 0;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Invalid date";
    }
}
