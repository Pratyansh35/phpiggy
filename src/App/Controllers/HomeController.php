<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TransactionService;
use Framework\TemplateEngine;

class HomeController
{
    public function __construct(
        private readonly TemplateEngine $view,
        private readonly TransactionService $s_transaction,
    )
    {}

    public function home(): void
    {
        $page = $_GET['page'] ?? 1;
        $page = (int) $page;
        $length = 3;
        $offset = ($page - 1) * $length;
        $search_term = $_GET['s'] ?? null;

        [$transactions, $count] = $this->s_transaction->getUserTransactions($length, $offset);

        $last_page = ceil($count / $length);
        $pages = $last_page ? range(1, $last_page) : [];

        $page_links = array_map(
            fn($page_num) => http_build_query([
                'page' => $page_num,
                's' => $search_term,
            ]),
            $pages
        );

        echo $this->view->render("/index.php", [
            'title' => 'Home',
            'current_page' => $page,
            'previous_page_query' => http_build_query(['page' => $page - 1, 's' => $search_term]),
            'next_page_query' => http_build_query(['page' => $page + 1, 's' => $search_term]),
            'transactions' => $transactions,
            'count' => $count,
            'last_page' => $last_page,
            'page_links' => $page_links,
            'search_term' => $search_term,
        ]);
    }
}
