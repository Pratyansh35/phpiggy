<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\{ValidatorService, TransactionService};
use Framework\TemplateEngine;

class TransactionController
{
    public function __construct(
        private readonly TemplateEngine $view,
        private readonly ValidatorService $s_validator,
        private readonly TransactionService $s_transaction,
    )
    {}

    public function createView(): void
    {
        echo $this->view->render("transactions/create.php", [
            'title' => 'Create Transaction'
        ]);
    }

    public function createStore(): void
    {
        $this->s_validator->validateTransaction($_POST);

        $this->s_transaction->create($_POST);

        redirectTo('/');
    }

    public function editForm(array $params): void
    {
        $transaction = $this->s_transaction->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo('/');
        }

        echo $this->view->render("transactions/edit.php", [
            'title' => 'Edit Transaction',
            'transaction' => $transaction
        ]);
    }

    public function editStore(array $params): void
    {
        $transaction = $this->s_transaction->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo('/');
        }

        $this->s_validator->validateTransaction($_POST);

        $this->s_transaction->update($_POST, $transaction['id']);

        redirectTo('/');
    }

    public function delete(array $params): void
    {
        $this->s_transaction->delete((int) $params['transaction']);

        redirectTo('/');
    }
}
