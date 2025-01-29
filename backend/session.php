<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';

$session_token = $_POST['session_token'] ?? null;

if (!empty($session_token)) {
    $statement = $database_connection->prepare("SELECT nama_lengkap FROM users WHERE session_token = ?");
    $statement->execute([$session_token]);
    $user = $statement->fetch();

    if ($user) {
        echo json_encode(['status' => 'success', 'hasil' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Session tidak valid']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
