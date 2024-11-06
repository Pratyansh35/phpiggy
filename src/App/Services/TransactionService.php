<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
    public function __construct(
        private readonly Database $db,
    )
    {}

    public function create(array $form_data): void
    {
        $this->db->query(
    "INSERT INTO transactions(user_id, description, amount, date) VALUES(:user_id, :description, :amount, :date)",
          [
              'user_id' => $_SESSION['user'],
              'description' => $form_data['description'],
              'amount' => $form_data['amount'],
              'date' => "{$form_data['date']} 00:00:00",
          ]
        );
    }

    public function getUserTransactions(int $length, int $offset): bool | array
    {
        $search = addcslashes($_GET['s'] ?? '', '%_');
        $params =  [
            'user_id' => $_SESSION['user'],
            'description' => "%$search%",
        ];

        $transactions = $this->db->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date 
                    FROM transactions 
                    WHERE user_id = :user_id 
                    AND description LIKE :description
                    LIMIT {$length} OFFSET {$offset}",
            $params
        )->findAll();

        $transactions = array_map(function (array $transaction) {
            $transaction['receipts'] = $this->db->query(
                "SELECT * FROM receipts WHERE transaction_id = :transaction_id",
                ['transaction_id' => $transaction['id']]
            )->findAll();

            return $transaction;
        }, $transactions);

        $transactions_count = $this->db->query(
            "SELECT COUNT(*) FROM transactions  WHERE user_id = :user_id AND description LIKE :description",
                $params
        )->count();

        return [
            $transactions,
            $transactions_count
        ];
    }

    public function getUserTransaction(string $id)
    {
        return $this->db->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user'],
            ]
        )->find();
    }

    public function update(array $form_data, int $id): void
    {
        $this->db->query(
            "UPDATE transactions SET description = :description, amount = :amount, date = :date WHERE id = :id AND user_id = :user_id",
            [
                'user_id' => $_SESSION['user'],
                'id' => $id,
                'description' => $form_data['description'],
                'amount' => $form_data['amount'],
                'date' => "{$form_data['date']} 00:00:00",
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->db->query(
            "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user'],
            ]
        );
    }
}
