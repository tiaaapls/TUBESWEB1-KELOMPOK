<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';

$session_token = $_POST['session_token'] ?? null;

if (!empty($session_token)) {
    $updateStatement = $database_connection->prepare("UPDATE users SET session_token = NULL WHERE session_token = ?");
    $updateStatement->execute([$session_token]);

    if ($updateStatement->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Logout berhasil']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Token sesi tidak valid']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
