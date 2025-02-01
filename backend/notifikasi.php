<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'koneksi/config.php';
include 'session.php';

// Validasi sesi
$user_id = validateSession();

try {
    // Ambil tugas yang akan segera jatuh tempo (dalam 3 hari)
    $query_deadline = "
        SELECT id, judul, deskripsi, deadline 
        FROM tasks 
        WHERE user_id = ? 
        AND status != 'selesai' 
        AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
        ORDER BY deadline ASC
    ";
    $stmt_deadline = $database_connection->prepare($query_deadline);
    $stmt_deadline->execute([$user_id]);
    $tugas_deadline = $stmt_deadline->fetchAll();

    // Hitung tugas belum selesai
    $query_belum_selesai = "
        SELECT COUNT(*) as jumlah 
        FROM tasks 
        WHERE user_id = ? 
        AND status = 'belum selesai'
    ";
    $stmt_belum_selesai = $database_connection->prepare($query_belum_selesai);
    $stmt_belum_selesai->execute([$user_id]);
    $jumlah_belum_selesai = $stmt_belum_selesai->fetch()['jumlah'];

    // Hitung tugas sedang dikerjakan
    $query_sedang_dikerjakan = "
        SELECT COUNT(*) as jumlah 
        FROM tasks 
        WHERE user_id = ? 
        AND status = 'sedang dikerjakan'
    ";
    $stmt_sedang_dikerjakan = $database_connection->prepare($query_sedang_dikerjakan);
    $stmt_sedang_dikerjakan->execute([$user_id]);
    $jumlah_sedang_dikerjakan = $stmt_sedang_dikerjakan->fetch()['jumlah'];

    // Hitung tugas selesai hari ini
    $query_selesai_hari_ini = "
        SELECT COUNT(*) as jumlah 
        FROM tasks 
        WHERE user_id = ? 
        AND status = 'selesai' 
        AND DATE(updated_at) = CURDATE()
    ";
    $stmt_selesai_hari_ini = $database_connection->prepare($query_selesai_hari_ini);
    $stmt_selesai_hari_ini->execute([$user_id]);
    $jumlah_selesai_hari_ini = $stmt_selesai_hari_ini->fetch()['jumlah'];

    // Kirim respons JSON
    echo json_encode([
        'status' => 'success',
        'data' => [
            'tugas_deadline_segera' => $tugas_deadline,
            'statistik' => [
                'belum_selesai' => (int)$jumlah_belum_selesai,
                'sedang_dikerjakan' => (int)$jumlah_sedang_dikerjakan,
                'selesai_hari_ini' => (int)$jumlah_selesai_hari_ini
            ]
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kesalahan database: ' . $e->getMessage()
    ]);
}
?>
