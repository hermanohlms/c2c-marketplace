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

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rawPassword = $_POST['password'] ?? '';

            $role = $_POST['role'] ?? 'buyer';

            $allowedRoles = ['buyer', 'seller'];

            if (!in_array($role, $allowedRoles)) {
                $role = 'buyer';
            }

            // Validation

            if ($name === '' || $email === '' || $rawPassword === '') {
                $_SESSION['error'] = "All fields are required.";
                header("Location: /index.php?page=register");
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Please enter a valid email address.";
                header("Location: /index.php?page=register");
                exit;
            }

            if (strlen($rawPassword) < 6) {
                $_SESSION['error'] = "Password must be at least 6 characters long.";
                header("Location: /index.php?page=register");
                exit;
            }

            $password = password_hash(
                $rawPassword,
                PASSWORD_DEFAULT
            );

            $userModel = new User($this->db);

            try {

                $created = $userModel->create(
                    $name,
                    $email,
                    $password,
                    $role
                );

                if ($created) {

                    $_SESSION['success'] =
                        "Account created successfully. Please login.";

                    header("Location: /index.php?page=login");
                    exit;
                }

                $_SESSION['error'] =
                    "Registration failed. Please try again.";

                header("Location: /index.php?page=register");
                exit;
            } catch (PDOException $e) {

                if ($e->getCode() === '23505') {

                    $_SESSION['error'] =
                        "An account with this email already exists.";

                    header("Location: /index.php?page=register");
                    exit;
                }

                $_SESSION['error'] =
                    "Registration failed. Please try again.";

                header("Location: /index.php?page=register");
                exit;
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
                    $_SESSION['profile_image'] = $user['profile_image'] ?? null;

                    if ($user['role'] === 'seller') {
                        header("Location: /index.php?page=dashboard");
                        exit;
                    }

                    if ($user['role'] === 'buyer') {
                        header("Location: /index.php?page=shop");
                        exit;
                    }

                    if ($user['role'] === 'admin') {
                        header("Location: /index.php?page=admin-dashboard");
                        exit;
                    }
                } else {

                    echo "Incorrect password.";
                }
            } else {

                $_SESSION['error'] = "Incorrect email or password.";

                header("Location: /index.php?page=login");
                exit;
            }
        }
    }
}
