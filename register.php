<?php
header("Content-Type: application/json");
include 'koneksi.php';

$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    response(false, "Semua kolom harus diisi");
}

// 1. Cek apakah email/username sudah ada
$check = $connect->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $username);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    response(false, "Username atau Email sudah terdaftar");
}

// 2. Hash Password (Wajib untuk keamanan!)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 3. Insert User Baru (Status is_verified = 0)
$stmt = $connect->prepare("INSERT INTO users (username, email, password, role, is_verified) VALUES (?, ?, ?, 'user', 0)");
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    // 4. Generate OTP (4 digit)
    $otp_code = rand(1000, 9999);

    // Simpan OTP ke database
    $stmt_otp = $connect->prepare("INSERT INTO otp_codes (email, code) VALUES (?, ?)");
    $stmt_otp->bind_param("ss", $email, $otp_code);
    $stmt_otp->execute();

    // TODO: Di sini Anda masukkan script kirim Email (PHPMailer)
    // Untuk sementara, kita kirim kode OTP di respon JSON agar Frontend bisa testing dulu

    response(true, "Registrasi berhasil. Silakan cek email untuk verifikasi.", [
        "debug_otp" => $otp_code // Hapus baris ini nanti saat production!
    ]);
} else {
    response(false, "Gagal mendaftar");
}
