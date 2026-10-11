<?php

$host = "localhost";
$dbname = "mastertee";
$username = "root";
$password = ""; // Replace with your MySQL password

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    error_log($e->getMessage());

    die("Database connection failed. Please contact support.");

}

?>