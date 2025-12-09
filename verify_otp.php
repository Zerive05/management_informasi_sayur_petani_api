<?php
header("Content-Type: application/json");
include 'koneksi.php';

$email = $_POST['email'] ?? '';
$code  = $_POST['otp_code'] ?? '';

// 1. Cek kecocokan OTP di database
$stmt = $connect->prepare("SELECT * FROM otp_codes WHERE email = ? AND code = ?");
$stmt->bind_param("ss", $email, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 2. Jika cocok, Update status user menjadi verified
    $update = $connect->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
    $update->bind_param("s", $email);
    $update->execute();

    // 3. Hapus OTP agar tidak bisa dipakai 2x
    $delete = $connect->prepare("DELETE FROM otp_codes WHERE email = ?");
    $delete->bind_param("s", $email);
    $delete->execute();

    response(true, "Verifikasi Berhasil!");
} else {
    response(false, "Kode OTP salah atau kadaluarsa");
}
