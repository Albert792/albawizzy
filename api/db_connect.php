<?php
/* ============================================================
   db_connect.php
   Shared MySQL connection (PDO) for the SimuKwaMkopo API.
   Update these settings to match your MySQL setup (defaults
   below match a typical local XAMPP/WAMP install).
   ============================================================ */

$host = "localhost";
$dbname = "simukwamkopo";
$dbuser = "root";
$dbpass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Imeshindwa kuunganisha na database: " . $e->getMessage()]);
    exit;
}