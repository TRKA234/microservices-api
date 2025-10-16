<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Ambil body JSON
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nama) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$nama = $data->nama;
$email = $data->email;
$password = password_hash($data->password, PASSWORD_BCRYPT);

// Cek apakah email sudah ada (prepared)
$cekStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$cekStmt) {
    echo json_encode(['status' => 'error', 'message' => 'Server error']);
    exit;
}
$cekStmt->bind_param('s', $email);
$cekStmt->execute();
$cekResult = $cekStmt->get_result();

if ($cekResult->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar']);
    exit;
}

// Insert ke database (prepared)
$stmt = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param('sss', $nama, $email, $password);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal registrasi']);
    }
} else {
    echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil']);
}
