<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    public function __construct(
        private readonly Database $db,
    )
    {}

    /**
     * @param string $email
     * @return bool
     */
    public function isEmailTaken(string $email): bool
    {
        $email_count = $this->db->query("SELECT COUNT(*) FROM users WHERE email = :email", [
            'email' => $email,
        ])->count();

        if ($email_count > 0) {
            throw new ValidationException(['email' => 'Email taken!']);
        }

        return false;
    }

    public function create(array $form_data): void
    {
        $this->db->query("INSERT INTO users(email,password,age,country,social_media_url) VALUES(:email, :password, :age, :country, :social_media_url)", [
            'email' => $form_data['email'],
            'password' => password_hash($form_data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'age' => $form_data['age'],
            'country' => $form_data['country'],
            'social_media_url' => $form_data['socialMediaURL'],
        ]);

        session_regenerate_id();

        $_SESSION['user'] = $this->db->id();
    }

    public function login(array $form_data): void
    {
        $user = $this->db->query("SELECT id, password, email FROM users WHERE email = :email", [
            'email' => $form_data['email'],
        ])->find();

        $passwords_match = password_verify($form_data['password'], $user['password'] ?? '');

        if (!$user || !$passwords_match) {
            throw new ValidationException(['password' => ['Invalid Credentials!']]);
        }

        session_regenerate_id();

        $_SESSION['user'] = $user['id'];
    }

    public function logout(): void
    {
        // unset($_SESSION['user']);
        // session_regenerate_id();
        session_destroy();

        $params = session_get_cookie_params();
        setcookie(
            'PHPSESSID',
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly'],
        );
    }
}
