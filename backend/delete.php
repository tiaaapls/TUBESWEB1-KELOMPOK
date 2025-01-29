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

// Get DELETE data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID tugas tidak diberikan']);
    exit();
}

try {
    // Prepare SQL statement to delete task
    $stmt = $database_connection->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    
    // Execute the statement
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
