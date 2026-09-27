<?php
/* ============================================================
   api/logout.php
   Destroys the admin session.
   ============================================================ */

session_start();
$_SESSION = [];
session_destroy();

header('Content-Type: application/json');
echo json_encode(["success" => true]);