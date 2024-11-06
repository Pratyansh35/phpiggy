<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Validator;
use Framework\Rules\{
    DateFormatRule,
    MaxRule,
    NumericRule,
    RequiredRule,
    EmailRule,
    MinRule,
    InRule,
    UrlRule,
    MinLengthRule,
    MatchRule
};

class ValidatorService
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->validator->add('required', new RequiredRule());
        $this->validator->add('email', new EmailRule());
        $this->validator->add('min', new MinRule());
        $this->validator->add('in', new InRule());
        $this->validator->add('url', new UrlRule());
        $this->validator->add('min_length', new MinLengthRule());
        $this->validator->add('match', new MatchRule());
        $this->validator->add('max_length', new MaxRule());
        $this->validator->add('numeric', new NumericRule());
        $this->validator->add('date_format', new DateFormatRule());
    }

    public function validateRegister(array $form_data): void
    {
        $this->validator->validate($form_data, [
            'email' => ['required', 'email'],
            'age' => ['required', 'min:18'],
            'country' => ['required', 'in:USA,Canada,Mexico'],
            'socialMediaURL' => ['required', 'url'],
            'password' => ['required', 'min_length:6'],
            'confirm' => ['required', 'match:password'],
            'accept' => ['required'],
        ]);
    }

    public function validateLogin(array $form_data): void
    {
        $this->validator->validate($form_data, [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
    }

    public function validateTransaction(array $form_data): void
    {
        $this->validator->validate($form_data, [
            'description' => ['required', 'min_length:3', 'max_length:255'],
            'amount' => ['required', 'min:1', 'numeric'],
            'date' => ['required', 'date_format:Y-m-d']
        ]);
    }
}
