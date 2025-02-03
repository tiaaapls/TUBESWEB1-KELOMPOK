<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'koneksi/config.php';
include 'session.php';

$statement = $database_connection->prepare("SELECT COUNT(*) AS `jumlah_tugas_belumSelesai` FROM `tasks` WHERE `status` != 'selesai';");
$statement->execute();

$data = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}
echo json_encode($data);
?>
