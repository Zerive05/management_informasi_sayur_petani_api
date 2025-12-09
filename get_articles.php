<?php
header("Content-Type: application/json");
include 'koneksi.php';

// Ambil semua artikel + nama penulisnya
$sql = "SELECT articles.id, articles.title, articles.content, articles.image_url, articles.created_at, users.username as author 
        FROM articles 
        JOIN users ON articles.author_id = users.id 
        ORDER BY articles.created_at DESC";

$result = $connect->query($sql);

$articles = array();
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

response(true, "List Artikel", $articles);
