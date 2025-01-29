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

// Get PUT data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID tugas harus diberikan']);
    exit();
}

// Check if at least one updateable field is provided
if (!isset($data['judul']) && !isset($data['deskripsi']) && !isset($data['status']) && !isset($data['deadline'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang akan diperbarui']);
    exit();
}

try {
    // Prepare dynamic update query
    $updateFields = [];
    $params = [];

    // Build update fields dynamically
    if (isset($data['judul'])) {
        $updateFields[] = "judul = ?";
        $params[] = $data['judul'];
    }
    if (isset($data['deskripsi'])) {
        $updateFields[] = "deskripsi = ?";
        $params[] = $data['deskripsi'];
    }
    if (isset($data['status'])) {
        // Validate status
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
        // Optional: validate date format
        if ($data['deadline'] !== null && !strtotime($data['deadline'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid']);
            exit();
        }
        $updateFields[] = "deadline = ?";
        $params[] = $data['deadline'];
    }

    // Add task ID and user_id to params
    $params[] = $data['id'];
    $params[] = $user_id;

    // Prepare and execute update statement
    $stmt = $database_connection->prepare("UPDATE tasks SET " . implode(', ', $updateFields) . " WHERE id = ? AND user_id = ?");
    $result = $stmt->execute($params);

    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Tugas berhasil diperbarui'
        ]);
    } elseif ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Tugas tidak ditemukan atau tidak memiliki izin'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui tugas']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kesalahan database: ' . $e->getMessage()
    ]);
}
?>
