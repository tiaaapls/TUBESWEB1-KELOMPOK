<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';

// Ambil data dari form-data (POST)
$namaLengkap = $_POST["nama_lengkap"] ?? null;
$email = $_POST["email"] ?? null;
$username = $_POST["user"] ?? null;
$password = $_POST["pwd"] ?? null;

// Validasi input
if (!$namaLengkap || !$email || !$username || !$password) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Mohon isi semua kolom']);
    exit();
}

try {
    // Cek apakah username atau email sudah terdaftar
    $checkStmt = $database_connection->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $checkStmt->execute(['username' => $username, 'email' => $email]);

    if ($checkStmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Username atau email sudah digunakan']);
        exit();
    }

    // Hash password dan simpan ke database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $insertStmt = $database_connection->prepare("INSERT INTO users (nama_lengkap, email, username, password) VALUES (:nama, :email, :username, :password)");
    $insertStmt->execute(['nama' => $namaLengkap, 'email' => $email, 'username' => $username, 'password' => $hashedPassword]);

    echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan server']);
}
?>
