<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'koneksi/config.php';
include 'session.php';

// Validasi session dan mendapatkan user_id
$user_id = validateSession();
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// Mengambil nilai dari input
$judul = $data['judul'] ?? null;
$deskripsi = $data['deskripsi'] ?? null;
$status = $data['status'] ?? 'belum selesai';
$deadline = $data['deadline'] ?? null;

// Validasi input
if (empty($judul) || empty($deskripsi)) {
    echo json_encode(['status' => 'error', 'message' => 'Judul dan deskripsi tugas harus diisi']);
    exit();
}

try {
    // Menyimpan tugas ke database
    $stmt = $database_connection->prepare(
        "INSERT INTO tasks (user_id, judul, deskripsi, status, created_at, deadline) VALUES (?, ?, ?, ?, NOW(), ?)"
    );
    $stmt->execute([$user_id, $judul, $deskripsi, $status, $deadline]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Tugas berhasil ditambahkan',
        'id' => $database_connection->lastInsertId()
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan database: ' . $e->getMessage()]);
}
?>