<?php

$host = "db";
$dbname = "ecommerce";
$user = "user";
$password = "user123";

try {

    $conn = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Connection failed: " . $e->getMessage());

}