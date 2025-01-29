<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';

$namaLengkap = $_POST["nama_lengkap"] ?? null;
$email = $_POST["email"] ?? null;
$username = $_POST["user"] ?? null;
$password = $_POST["pwd"] ?? null;

if (!empty($namaLengkap) && !empty($email) && !empty($username) && !empty($password)) {
    try {
        $checkStatement = $database_connection->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStatement->execute([$username, $email]);

        if ($checkStatement->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Username atau email sudah digunakan']);
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertStatement = $database_connection->prepare("INSERT INTO users (nama_lengkap, email, username, password) VALUES (?, ?, ?, ?)");
            $insertStatement->execute([$namaLengkap, $email, $username, $hashedPassword]);

            echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Mohon isi semua kolom']);
}
?>
