<?php
header("Content-Type: application/json");
include 'koneksi.php';

// IP Laptop Anda (Gunakan IP yang sama)
$base_url = "http://192.168.1.13/api_mispet/uploads/";

$title     = $_POST['title'] ?? '';
$content   = $_POST['content'] ?? '';
$author_id = $_POST['author_id'] ?? ''; // ID Admin yang login

if (empty($title) || empty($content) || empty($author_id)) {
    response(false, "Judul, Konten, dan Author ID harus diisi");
}

// 1. Cek Apakah User adalah Admin? (Keamanan)
$cek_admin = $connect->query("SELECT role FROM users WHERE id = '$author_id'");
$user = $cek_admin->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    response(false, "Hanya Admin yang boleh memposting artikel!");
}

// 2. Upload Gambar Artikel
$image_url = null; // Default null jika tidak ada gambar
if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $filename = "article_" . time() . ".jpg";
    $target_dir = "uploads/";

    // Auto-create folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename)) {
        $image_url = $base_url . $filename;
    } else {
        response(false, "Gagal upload gambar artikel");
    }
}

// 3. Simpan ke Database
$stmt = $connect->prepare("INSERT INTO articles (title, content, image_url, author_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $title, $content, $image_url, $author_id);

if ($stmt->execute()) {
    response(true, "Artikel berhasil diterbitkan");
} else {
    response(false, "Gagal menyimpan artikel");
}
