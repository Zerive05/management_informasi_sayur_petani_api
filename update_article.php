<?php
header("Content-Type: application/json");
include 'koneksi.php';

$base_url = "http://192.168.1.19/api_mispet/uploads/";

$id      = $_POST['id'] ?? ''; // ID Artikel yang mau diedit
$title   = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (empty($id) || empty($title) || empty($content)) {
    response(false, "ID Artikel, Judul, dan Konten wajib diisi");
}

// Logic Upload Gambar Baru (Opsional)
$image_query = "";
if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $filename = "article_" . time() . "_updated.jpg";
    $target_dir = "uploads/";

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename)) {
        $full_url = $base_url . $filename;
        $image_query = ", image_url = '$full_url'";
    }
}

// Update Data
$sql = "UPDATE articles SET title = ?, content = ? $image_query WHERE id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("ssi", $title, $content, $id);

if ($stmt->execute()) {
    response(true, "Artikel berhasil diperbarui");
} else {
    response(false, "Gagal update artikel");
}
