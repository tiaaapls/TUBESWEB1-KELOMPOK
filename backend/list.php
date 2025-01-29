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

// Optional filter for status
$status = $_GET['status'] ?? null;

try {
    // Prepare base SQL statement
    $query = "SELECT * FROM tasks WHERE user_id = ?";
    $params = [$user_id];

    // Add status filter if provided
    if ($status) {
        $query .= " AND status = ?";
        $params[] = $status;
    }

    // Add sorting
    $query .= " ORDER BY 
        CASE 
            WHEN status = 'belum selesai' THEN 1 
            WHEN status = 'sedang dikerjakan' THEN 2 
            ELSE 3 
        END, 
        deadline ASC, 
        created_at DESC";

    // Prepare and execute statement
    $stmt = $database_connection->prepare($query);
    $stmt->execute($params);
    
    // Fetch all tasks
    $tasks = $stmt->fetchAll();

    // Return tasks as JSON
    echo json_encode([
        'status' => 'success', 
        'data' => $tasks,
        'total' => count($tasks)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kesalahan database: ' . $e->getMessage()
    ]);
}
?>
