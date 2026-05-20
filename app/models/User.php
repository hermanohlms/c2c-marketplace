<?php

class User
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $email, $password, $role)
    {
        $sql = "
            INSERT INTO users
            (name, email, password, role)
            VALUES
            (:name, :email, :password, :role)
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);
    }

    public function findByEmail($email)
    {
        $sql = "
        SELECT * FROM users
        WHERE email = :email
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole($user_id, $role)
    {
        $allowedRoles = ['buyer', 'seller', 'admin'];

        if (!in_array($role, $allowedRoles)) {
            return false;
        }

        $sql = "
        UPDATE users
        SET role = :role
        WHERE id = :user_id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':role' => $role,
            ':user_id' => $user_id
        ]);
    }

    public function updateStatus($user_id, $status)
    {
        $allowedStatuses = ['active', 'inactive'];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $sql = "
        UPDATE users
        SET status = :status
        WHERE id = :user_id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':user_id' => $user_id
        ]);
    }

    public function findById($id)
    {
        $sql = "
        SELECT *
        FROM users
        WHERE id = :id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $name, $phone, $profile_image = null)
    {
        if ($profile_image) {
            $sql = "
        UPDATE users
        SET name = :name,
            phone = :phone,
            profile_image = :profile_image
        WHERE id = :id
    ";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':name' => $name,
                ':phone' => $phone,
                ':profile_image' => $profile_image,
                ':id' => $id
            ]);
        }

        $sql = "
        UPDATE users
        SET name = :name,
            phone = :phone
        WHERE id = :id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':id' => $id
        ]);
    }

    public function findSellerById($id)
    {
        $sql = "
        SELECT id, name, email, phone, profile_image, created_at
        FROM users
        WHERE id = :id
        AND role = 'seller'
        AND status = 'active'
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
