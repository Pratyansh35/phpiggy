<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReceiptService;
use App\Services\TransactionService;
use Framework\TemplateEngine;

class ReceiptController
{
    public function __construct(
        private readonly TemplateEngine $view,
        private readonly TransactionService $s_transaction,
        private readonly ReceiptService $s_receipt,
    ) {
    }

    public function uploadView(array $params): void
    {
        $transaction = $this->s_transaction->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo("/");
        }

        echo $this->view->render("receipts/create.php");
    }

    /**
     * @throws \Exception
     */
    public function upload(array $params): void
    {
        $transaction = $this->s_transaction->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo("/");
        }

        $receipt_file = $_FILES['receipt'] ?? null;

        $this->s_receipt->validateFile($receipt_file);

        $this->s_receipt->upload($receipt_file, $transaction['id']);

        redirectTo("/");
    }

    public function download(array $params): void
    {
        $transaction = $this->s_transaction->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo("/");
        }

        $receipt = $this->s_receipt->getReceipt((int)$params['receipt']);

        if (empty($receipt) || $receipt['transaction_id'] !== $transaction['id']) {
            redirectTo("/");
        }

        $this->s_receipt->read($receipt);
    }

    public function delete(array $params): void
    {
        $transaction = $this->s_transaction->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo("/");
        }

        $receipt = $this->s_receipt->getReceipt((int)$params['receipt']);

        if (empty($receipt) || $receipt['transaction_id'] !== $transaction['id']) {
            redirectTo("/");
        }

        $this->s_receipt->delete($receipt);

        redirectTo("/");
    }
}
