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

// Validasi input bulan
if (!isset($_POST['bulan'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bulan parameter tidak ditemukan']);
    exit();
}

$bulan = $_POST['bulan'];

// Menentukan bulan dalam format numerik
$bulan_map = [
    'Januari' => 1, 
    'Februari' => 2, 
    'Maret' => 3, 
    'April' => 4,
    'Mei' => 5, 
    'Juni' => 6, 
    'Juli' => 7, 
    'Agustus' => 8,
    'September' => 9, 
    'Oktober' => 10, 
    'November' => 11, 
    'Desember' => 12
];

$bulan_numerik = $bulan_map[$bulan] ?? null;
if (!$bulan_numerik) {
    http_response_code(400);
    echo json_encode(['error' => 'Bulan tidak valid']);
    exit();
}

// Mendapatkan tahun saat ini jika tidak diberikan
$tahun = isset($_POST['tahun']) ? (int) $_POST['tahun'] : date('Y');

// Query untuk menghitung jumlah tugas berdasarkan minggu dalam bulan tertentu
$statement = $database_connection->prepare("
    SELECT
        FLOOR((DAYOFMONTH(tasks.created_at) - 1) / 7) + 1 AS minggu,
        COALESCE(COUNT(tasks.id), 0) AS jumlah_tugas
    FROM tasks
    WHERE MONTH(tasks.created_at) = ? AND YEAR(tasks.created_at) = ? AND user_id = ?
    GROUP BY minggu
    ORDER BY minggu;
");
$statement->execute([$bulan_numerik, $tahun, $user_id]);

$data = $statement->fetchAll(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode(['status' => 'success', 'data' => $data]);
?>