<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';
include 'session.php';

// Validate session
$user_id = validateSession();
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['judul']) || !isset($data['deskripsi'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Judul dan deskripsi tugas harus diisi']);
    exit();
}

try {
    // Prepare SQL statement
    $stmt = $database_connection->prepare("INSERT INTO tasks (user_id, judul, deskripsi, status, created_at, deadline) VALUES (?, ?, ?, ?, NOW(), ?)");
    
    // Set default status and handle optional deadline
    $status = $data['status'] ?? 'belum selesai';
    $deadline = $data['deadline'] ?? null;

    // Execute the statement
    $result = $stmt->execute([
        $user_id, 
        $data['judul'], 
        $data['deskripsi'], 
        $status,
        $deadline
    ]);

    if ($result) {
        $task_id = $database_connection->lastInsertId();
        echo json_encode([
            'status' => 'success', 
            'message' => 'Tugas berhasil ditambahkan',
            'id' => $task_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan tugas']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kesalahan database: ' . $e->getMessage()
    ]);
}
?>
