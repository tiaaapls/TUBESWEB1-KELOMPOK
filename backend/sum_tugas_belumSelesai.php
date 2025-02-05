<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
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

$statement = $database_connection->prepare("SELECT COUNT(*) AS `jumlah_tugas_belumSelesai` FROM `tasks` WHERE `status` != 'selesai' AND `user_id` = ?;");
$statement->execute([$user_id]);

$data = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}
echo json_encode($data);
?>