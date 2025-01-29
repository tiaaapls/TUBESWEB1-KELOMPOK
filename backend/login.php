<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';

$username = $_POST["user"] ?? null;
$password = $_POST["pwd"] ?? null;

if (!empty($username) && !empty($password)) {
    $statement = $database_connection->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $statement->execute([$username]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $session_token = bin2hex(random_bytes(32));
        $updateStatement = $database_connection->prepare("UPDATE users SET session_token = ? WHERE id = ?");
        $updateStatement->execute([$session_token, $user['id']]);

        echo json_encode(['status' => 'success', 'session_token' => $session_token]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username atau password salah']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Mohon isi semua kolom']);
}
?>
