<?php
// File: update_profile.php
header("Content-Type: application/json");
include 'koneksi.php';

// IP Laptop Anda (Jangan berubah saat presentasi nanti!)
$base_url = "http://192.168.1.13/api_mispet/uploads/";

$id  = $_POST['id'] ?? '';
$bio = $_POST['bio'] ?? '';

if (empty($id)) {
    response(false, "ID User tidak valid");
}

// Logic Upload Foto
$photo_query = "";
if (isset($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $filename = "user_" . $id . "_" . time() . ".jpg";
    $target_dir = "uploads/";

    // Pastikan folder uploads ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Pindahkan file dari temp ke folder uploads
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $filename)) {
        // Gabungkan IP + nama file agar bisa dibuka di HP
        $full_url = $base_url . $filename;
        $photo_query = ", photo_url = '$full_url'";
    } else {
        response(false, "Gagal memindahkan file foto");
    }
}

// Update Database
$sql = "UPDATE users SET bio = ? $photo_query WHERE id = ?";

$stmt = $connect->prepare($sql);
$stmt->bind_param("si", $bio, $id);

if ($stmt->execute()) {
    // Ambil data user terbaru untuk dikembalikan ke frontend
    $cek = $connect->query("SELECT * FROM users WHERE id = $id");
    $user_baru = $cek->fetch_assoc();

    response(true, "Profil berhasil diperbarui", [
        "bio" => $user_baru['bio'],
        "photo_url" => $user_baru['photo_url']
    ]);
} else {
    response(false, "Gagal update profil: " . $stmt->error);
}
