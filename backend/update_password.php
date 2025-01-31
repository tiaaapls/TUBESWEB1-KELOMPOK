<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'koneksi/config.php';
include 'session.php';

// Ambil session_token dari header Authorization
$headers = getallheaders();
$session_token = null;
if (isset($headers['Authorization'])) {
    $session_token = trim(str_replace('Bearer', '', $headers['Authorization']));
}

// Validasi session token
$user_id = validateSessionFromToken($session_token);
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Ambil data dari form-data (multipart/form-data)
$current_password = $_POST['current_password'] ?? null;
$new_password = $_POST['new_password'] ?? null;
$confirm_password = $_POST['confirm_password'] ?? null;

// Validasi input
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom password harus diisi']);
    exit();
}

if ($new_password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password baru dan konfirmasi password tidak cocok']);
    exit();
}

// Ambil password saat ini dari database
$stmt = $database_connection->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !password_verify($current_password, $user['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password saat ini salah']);
    exit();
}

// Update password baru
$new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

try {
    $stmt = $database_connection->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$new_password_hashed, $user_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Password berhasil diperbarui'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan database: ' . $e->getMessage()]);
}
?>
