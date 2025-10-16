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

if (!isset($data->nama) || !isset($data->email) || !isset($data->phone) || !isset($data->jabatan) || !isset($data->departemen) || !isset($data->gaji) || !isset($data->tanggal_masuk) || !isset($data->status_karyawan)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Field wajib: nama, email, phone, jabatan, departemen, gaji, tanggal_masuk, status_karyawan'
    ]);
    exit;
}

$nama             = $data->nama;
$email            = $data->email;
$phone            = $data->phone;
$alamat           = isset($data->alamat) ? $data->alamat : null;
$tanggal_lahir    = isset($data->tanggal_lahir) ? $data->tanggal_lahir : null; // format YYYY-MM-DD
$jenis_kelamin    = isset($data->jenis_kelamin) ? $data->jenis_kelamin : null; // L/P atau lainnya
$jabatan          = $data->jabatan;
$departemen       = $data->departemen;
$gaji             = intval($data->gaji);
$tanggal_masuk    = $data->tanggal_masuk; // format YYYY-MM-DD
$status_karyawan  = $data->status_karyawan; // tetap/kontrak/intern
$foto_url         = isset($data->foto_url) ? $data->foto_url : null;

$stmt = $conn->prepare(
    "INSERT INTO pegawai (nama, email, phone, alamat, tanggal_lahir, jenis_kelamin, jabatan, departemen, gaji, tanggal_masuk, status_karyawan, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
if ($stmt) {
    $stmt->bind_param(
        'ssssssssisss',
        $nama,
        $email,
        $phone,
        $alamat,
        $tanggal_lahir,
        $jenis_kelamin,
        $jabatan,
        $departemen,
        $gaji,
        $tanggal_masuk,
        $status_karyawan,
        $foto_url
    );
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
