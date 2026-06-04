<?php

require_once __DIR__ . '/../models/User.php';

class ProfileController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }
    }

    public function show()
    {
        $this->requireLogin();

        $userModel = new User($this->db);
        $user = $userModel->findById($_SESSION['user_id']);

        require_once __DIR__ . '/../views/profile/show.php';
    }

    public function update()
    {
        $this->requireLogin();

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '') {
            $_SESSION['error'] = "Name is required.";
            header("Location: /public/index.php?page=profile");
            exit;
        }

        $profileImage = null;

        if (
            isset($_FILES['profile_image']) &&
            $_FILES['profile_image']['error'] === UPLOAD_ERR_OK
        ) {
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $maxSize = 2 * 1024 * 1024;

            if ($_FILES['profile_image']['size'] > $maxSize) {
                $_SESSION['error'] = "Profile image must be smaller than 2MB.";
                header("Location: /public/index.php?page=profile");
                exit;
            }

            $mimeType = mime_content_type($_FILES['profile_image']['tmp_name']);

            if (!array_key_exists($mimeType, $allowedMimeTypes)) {
                $_SESSION['error'] = "Only JPG, PNG, and WEBP profile images are allowed.";
                header("Location: /public/index.php?page=profile");
                exit;
            }

            $extension = $allowedMimeTypes[$mimeType];

            $profileImage =
                'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;

            move_uploaded_file(
                $_FILES['profile_image']['tmp_name'],
                __DIR__ . '/../../public/uploads/' . $profileImage
            );
        }

        $userModel = new User($this->db);

        $updated = $userModel->updateProfile(
            $_SESSION['user_id'],
            $name,
            $phone,
            $profileImage
        );

        if ($updated) {
            $_SESSION['user_name'] = $name;

            if ($profileImage) {
                $_SESSION['profile_image'] = $profileImage;
            }

            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Could not update profile.";
        }

        header("Location: /public/index.php?page=profile");
        exit;
    }
}
