<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Field id wajib diisi'
    ]);
    exit;
}

$id = intval($data->id);

$stmt = $conn->prepare("DELETE FROM pegawai WHERE id = ?");
if ($stmt) {
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data pegawai berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID tidak ditemukan'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}
