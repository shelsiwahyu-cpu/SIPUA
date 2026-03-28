<?php
// ============================================================
//  config.php — Koneksi Database SIPUA
// ============================================================

$host   = 'localhost';   // Server database (jangan diubah)
$dbname = 'sipua_db';    // Nama database
$user   = 'root';        // Username default XAMPP
$pass   = '';            // Password default XAMPP (kosong)

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi DB gagal: ' . $e->getMessage()]);
    exit;
}

// Header agar bisa diakses dari HTML (CORS)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }