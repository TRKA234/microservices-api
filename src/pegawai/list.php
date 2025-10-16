<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$query = "SELECT * FROM pegawai";
$result = $conn->query($query);

$pegawai = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pegawai[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $pegawai
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Tidak ada data',
        'data' => []
    ]);
}
