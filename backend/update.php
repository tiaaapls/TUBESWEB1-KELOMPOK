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

// Get data dari body request (JSON atau x-www-form-urlencoded)
$data = null;
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    // Jika data dikirim dalam format JSON
    $data = json_decode(file_get_contents('php://input'), true);
} else if ($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
    // Jika data dikirim dalam format x-www-form-urlencoded
    $data = $_POST;
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
    exit();
}

// Validasi input
if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID tugas harus diberikan']);
    exit();
}

// Cek apakah tugas ada milik pengguna
$stmt = $database_connection->prepare("SELECT user_id FROM tasks WHERE id = ?");
$stmt->execute([$data['id']]);
$task = $stmt->fetch();

if (!$task || $task['user_id'] != $user_id) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Tugas tidak ditemukan atau tidak memiliki izin']);
    exit();
}

// Cek dan update fields
$updateFields = [];
$params = [];

if (isset($data['judul'])) {
    $updateFields[] = "judul = ?";
    $params[] = $data['judul'];
}
if (isset($data['deskripsi'])) {
    $updateFields[] = "deskripsi = ?";
    $params[] = $data['deskripsi'];
}
if (isset($data['status'])) {
    $validStatuses = ['belum selesai', 'sedang dikerjakan', 'selesai'];
    if (!in_array($data['status'], $validStatuses)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Status tidak valid']);
        exit();
    }
    $updateFields[] = "status = ?";
    $params[] = $data['status'];
}
if (isset($data['deadline'])) {
    if ($data['deadline'] !== null && !strtotime($data['deadline'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid']);
        exit();
    }
    $updateFields[] = "deadline = ?";
    $params[] = $data['deadline'];
}

// Jika tidak ada field yang ingin diupdate
if (empty($updateFields)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang akan diperbarui']);
    exit();
}

$params[] = $data['id'];
$params[] = $user_id;

// Prepare dan eksekusi query update
$stmt = $database_connection->prepare("UPDATE tasks SET " . implode(', ', $updateFields) . " WHERE id = ? AND user_id = ?");
$result = $stmt->execute($params);

// Mengecek hasil eksekusi
if ($result && $stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Tugas berhasil diperbarui']);
} elseif ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Tugas tidak ditemukan atau tidak memiliki izin']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui tugas']);
}
?>
