<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'koneksi/config.php';
include 'session.php';

// Mengambil session_token dari header Authorization
$headers = getallheaders();
$session_token = $headers['Authorization'] ?? null;

if (!$session_token) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Session token diperlukan']);
    exit();
}

// Validasi session dan mendapatkan user_id
$user_id = validateSessionFromToken($session_token);
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Mengambil parameter pencarian dari query string
$judul = $_GET['judul'] ?? null;
$status = $_GET['status'] ?? null;

// Membuat query SQL dinamis berdasarkan parameter yang diberikan
$sql = "SELECT id, judul, deskripsi, status, created_at, deadline FROM tasks WHERE user_id = ?";
$params = [$user_id];

if ($judul) {
    $sql .= " AND judul LIKE ?";
    $params[] = "%$judul%";
}

if ($status) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY id ASC";

try {
    // Menjalankan query
    $stmt = $database_connection->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $tasks,
        'total' => count($tasks)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan database: ' . $e->getMessage()]);
}
?>
