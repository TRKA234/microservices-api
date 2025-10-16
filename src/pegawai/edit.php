<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->nama) || !isset($data->jabatan) || !isset($data->gaji)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Field id, nama, jabatan, gaji wajib diisi'
    ]);
    exit;
}

$id      = intval($data->id);
$nama    = $data->nama;
$jabatan = $data->jabatan;
$gaji    = intval($data->gaji);

$stmt = $conn->prepare("UPDATE pegawai SET nama = ?, jabatan = ?, gaji = ? WHERE id = ?");
if ($stmt) {
    $stmt->bind_param('ssii', $nama, $jabatan, $gaji, $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data pegawai berhasil diperbarui'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID tidak ditemukan atau tidak ada perubahan'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal update data'
        ]);
    }
} else {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error'
        ]);
    }
}
