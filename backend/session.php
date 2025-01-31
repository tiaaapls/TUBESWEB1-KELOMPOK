<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';

function validateSession() {
    global $database_connection;
    
    $session_token = $_POST['session_token'] ?? null;
    if (empty($session_token)) {
        echo json_encode(['status' => 'error', 'message' => 'Session token diperlukan']);
        exit();
    }

    // Mengecek validasi session token
    $statement = $database_connection->prepare("SELECT id FROM users WHERE session_token = ?");
    $statement->execute([$session_token]);
    $user = $statement->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Session tidak valid']);
        exit();
    }

    return $user['id'];
}

function validateSessionFromToken($session_token) {
    global $database_connection;

    if (empty($session_token)) {
        return null;
    }

    // Mengecek validasi session token
    $statement = $database_connection->prepare("SELECT id FROM users WHERE session_token = ?");
    $statement->execute([$session_token]);
    $user = $statement->fetch();

    return $user ? $user['id'] : null; // Mengembalikan user_id jika token valid
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_token'])) {
    $user_id = validateSession();
    echo json_encode(['status' => 'success', 'user_id' => $user_id]);
}
?>
