<?php
header("Content-Type: application/json");
include 'koneksi.php';

$email    = $_POST['email'] ?? ''; // Login pakai email lebih umum
$password = $_POST['password'] ?? '';

$stmt = $connect->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    // Cek Password Hash
    if (password_verify($password, $user['password'])) {

        // Cek apakah email sudah diverifikasi?
        if ($user['is_verified'] == 0) {
            response(false, "Email belum diverifikasi. Silakan masukkan OTP.");
        }

        response(true, "Login Berhasil", [
            "id"       => $user['id'],
            "username" => $user['username'],
            "role"     => $user['role'],
            "photo_url" => $user['photo_url']
        ]);
    } else {
        response(false, "Password salah");
    }
} else {
    response(false, "Email tidak ditemukan");
}
