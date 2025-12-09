<?php
// File: koneksi.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_mispet";

$connect = new mysqli($host, $user, $pass, $db);

if ($connect->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection Failed"]));
}

// Fungsi helper untuk kirim respon JSON
function response($success, $message, $data = null)
{
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data"    => $data
    ]);
    exit();
}
