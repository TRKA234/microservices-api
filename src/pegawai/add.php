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

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nama) || !isset($data->jabatan) || !isset($data->gaji)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Semua field wajib diisi: nama, jabatan, gaji'
    ]);
    exit;
}

$nama = $data->nama;
$jabatan = $data->jabatan;
$gaji = intval($data->gaji);

$stmt = $conn->prepare("INSERT INTO pegawai (nama, jabatan, gaji) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param('ssi', $nama, $jabatan, $gaji);
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data pegawai berhasil ditambahkan'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menambahkan data'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}
