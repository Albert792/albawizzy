<?php
/* ============================================================
   api/check-session.php
   Tells the front end whether an admin is currently logged in.
   ============================================================ */

session_start();
header('Content-Type: application/json');
echo json_encode(["loggedIn" => isset($_SESSION['admin_id'])]);