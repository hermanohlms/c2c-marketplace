<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];

            $userModel = new User($this->db);

            $created = $userModel->create(
                $name,
                $email,
                $password,
                $role
            );

            if ($created) {
                echo "User registered successfully!";
            } else {
                echo "Registration failed.";
            }
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = new User($this->db);

            $user = $userModel->findByEmail($email);

            if ($user) {

                if (password_verify($password, $user['password'])) {

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];

                    if ($user['role'] === 'seller') {
                        header("Location: /public/index.php?page=dashboard");
                        exit;
                    }

                    if ($user['role'] === 'buyer') {
                        header("Location: /public/index.php?page=shop");
                        exit;
                    }

                    if ($user['role'] === 'admin') {
                        header("Location: /public/index.php?page=admin-dashboard");
                        exit;
                    }
                } else {

                    echo "Incorrect password.";
                }
            } else {

                echo "User not found.";
            }
        }
    }
}
