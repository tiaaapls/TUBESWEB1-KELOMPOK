<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'koneksi/config.php';
include 'session.php';

// Ambil session_token dari header Authorization
$headers = getallheaders();
$session_token = null;
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

// Mengambil data dari form-data
$nama = $_POST['nama_lengkap'] ?? null;
$email = $_POST['email'] ?? null;
$username = $_POST['username'] ?? null;
$file = $_FILES['foto_profil'] ?? null;

// Mengambil data pengguna saat ini
$query = $database_connection->prepare("SELECT foto_profil FROM users WHERE id = ?");
$query->execute([$user_id]);
$user_data = $query->fetch(PDO::FETCH_ASSOC);
$foto_lama = $user_data['foto_profil'];

// Proses foto profil 
$foto_nama = $foto_lama; // Default tetap foto lama jika tidak ada perubahan
if ($file && $file['error'] == 0) {
    // Cek ekstensi file gambar
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Hanya file gambar yang diperbolehkan (jpg, jpeg, png, gif)']);
        exit();
    }

    // path untuk menyimpan foto
    $target_dir = "uploads/profiles/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Hapus foto lama jika ada
    if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
        unlink($target_dir . $foto_lama);
    }

    // Simpan foto baru
    $foto_nama = time() . '_' . basename($file['name']);
    $target_file = $target_dir . $foto_nama;

    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat meng-upload foto profil']);
        exit();
    }
}

try {
    // Update data profil
    $query_parts = [];
    $query_params = [];
    
    if ($nama) {
        $query_parts[] = "nama_lengkap = ?";
        $query_params[] = $nama;
    }
    if ($email) {
        $query_parts[] = "email = ?";
        $query_params[] = $email;
    }
    if ($username) {
        $query_parts[] = "username = ?";
        $query_params[] = $username;
    }
    if ($foto_nama !== $foto_lama) {
        $query_parts[] = "foto_profil = ?";
        $query_params[] = $foto_nama;
    }

    if (!empty($query_parts)) {
        $query_params[] = $user_id;
        $query = $database_connection->prepare("UPDATE users SET " . implode(", ", $query_parts) . " WHERE id = ?");
        $query->execute($query_params);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Profil berhasil diperbarui',
        'foto_profil' => $foto_nama ? "uploads/profiles/" . $foto_nama : null
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan database: ' . $e->getMessage()]);
}
?>
