<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';
include 'session.php';

// Ambil session_token dari header Authorization
$headers = getallheaders();
$session_token = null;

// Cek apakah token ada di header Authorization
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

// Ambil data dari body request (x-www-form-urlencoded)
$data = $_POST;

// Validasi input
if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID tugas tidak diberikan']);
    exit();
}

try {
    // Persiapkan statement SQL untuk menghapus tugas
    $stmt = $database_connection->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");

    // Eksekusi statement
    $result = $stmt->execute([
        $data['id'], 
        $user_id
    ]);

    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Tugas berhasil dihapus'
        ]);
    } elseif ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Tugas tidak ditemukan atau tidak memiliki izin'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus tugas']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kesalahan database: ' . $e->getMessage()
    ]);
}
?>
