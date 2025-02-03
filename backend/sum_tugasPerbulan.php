<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'koneksi/config.php';
include 'session.php';

if (!isset($_POST['bulan'])) {
    echo json_encode(['error' => 'Bulan parameter tidak ditemukan']);
    exit;
}

$bulan = $_POST['bulan']; // Mengambil bulan dari parameter POST

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

$bulan_numerik = isset($bulan_map[$bulan]) ? $bulan_map[$bulan] : null;

if (!$bulan_numerik) {
    echo json_encode(['error' => 'Bulan tidak valid']);
    exit;
}

// Mendapatkan tahun saat ini jika tidak diberikan
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y');  // Jika 'tahun' tidak diberikan, gunakan tahun sekarang

// Query untuk menghitung jumlah tugas berdasarkan minggu dalam bulan tertentu
$statement = $database_connection->prepare("
    SELECT
        FLOOR((DAYOFMONTH(tasks.created_at) - 1) / 7) + 1 AS minggu,
        COALESCE(COUNT(tasks.id), 0) AS jumlah_tugas
    FROM tasks
    WHERE MONTH(tasks.created_at) = ? AND YEAR(tasks.created_at) = ?
    GROUP BY minggu
    ORDER BY minggu;
");
$statement->execute([$bulan_numerik, date('Y')]);

$data = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
?>
