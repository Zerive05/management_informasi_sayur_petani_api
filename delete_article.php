<?php
header("Content-Type: application/json");
include 'koneksi.php';

$id = $_POST['id'] ?? '';

if (empty($id)) {
    response(false, "ID Artikel harus diisi");
}

// (Opsional) Hapus file gambar dari folder
// 1. Ambil URL gambar dulu dari database
$query = $connect->query("SELECT image_url FROM articles WHERE id = $id");
$data = $query->fetch_assoc();

// 2. Hapus data dari database
$stmt = $connect->prepare("DELETE FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Jika sukses delete DB, coba hapus filenya (Logic simpel)
    if (!empty($data['image_url'])) {
        // Mengubah URL http://... menjadi path lokal uploads/...
        $filename = basename($data['image_url']);
        $path = "uploads/" . $filename;
        if (file_exists($path)) {
            unlink($path); // Hapus file fisik
        }
    }
    response(true, "Artikel berhasil dihapus");
} else {
    response(false, "Gagal menghapus artikel");
}
