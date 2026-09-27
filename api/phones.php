<?php
/* ============================================================
   api/phones.php
   REST-style endpoint for phones.

   GET               -> list all phones (public)
   POST   (JSON body) -> add a phone            (admin only)
   PUT    (JSON body, includes "id") -> edit a phone (admin only)
   DELETE ?id=123    -> delete a phone           (admin only)
   ============================================================ */

session_start();
header('Content-Type: application/json');
require 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

function requireAdmin()
{
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "Huna ruhusa. Tafadhali ingia kama admin."]);
        exit;
    }
}

switch ($method) {

    case 'GET':
        $stmt = $pdo->query("SELECT * FROM phones ORDER BY id ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(["error" => "Jina la simu linahitajika."]);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO phones (name, description, image, deposit, monthly, months)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['image'] ?? 'images/phone.png',
            $data['deposit'] ?? 0,
            $data['monthly'] ?? 0,
            $data['months'] ?? 6
        ]);

        $stmt = $pdo->prepare("SELECT * FROM phones WHERE id = ?");
        $stmt->execute([$pdo->lastInsertId()]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        break;

    case 'PUT':
        requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Kitambulisho (id) cha simu kinahitajika."]);
            exit;
        }

        $stmt = $pdo->prepare(
            "UPDATE phones
             SET name = ?, description = ?, image = ?, deposit = ?, monthly = ?, months = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? 'images/phone.png',
            $data['deposit'] ?? 0,
            $data['monthly'] ?? 0,
            $data['months'] ?? 6,
            $id
        ]);

        $stmt = $pdo->prepare("SELECT * FROM phones WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        break;

    case 'DELETE':
        requireAdmin();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Kitambulisho (id) cha simu kinahitajika."]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM phones WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["success" => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method haitumiki."]);
}