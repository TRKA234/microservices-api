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

if (!isset($data->id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Field id wajib diisi'
    ]);
    exit;
}

$id = intval($data->id);

// Field opsional untuk update
$nama            = isset($data->nama) ? $data->nama : null;
$email           = isset($data->email) ? $data->email : null;
$phone           = isset($data->phone) ? $data->phone : null;
$alamat          = isset($data->alamat) ? $data->alamat : null;
$tanggal_lahir   = isset($data->tanggal_lahir) ? $data->tanggal_lahir : null;
$jenis_kelamin   = isset($data->jenis_kelamin) ? $data->jenis_kelamin : null;
$jabatan         = isset($data->jabatan) ? $data->jabatan : null;
$departemen      = isset($data->departemen) ? $data->departemen : null;
$gaji            = isset($data->gaji) ? intval($data->gaji) : null;
$tanggal_masuk   = isset($data->tanggal_masuk) ? $data->tanggal_masuk : null;
$status_karyawan = isset($data->status_karyawan) ? $data->status_karyawan : null;
$foto_url        = isset($data->foto_url) ? $data->foto_url : null;

// Bangun query dinamis berdasarkan field yang dikirim
$fields = [];
$params = [];
$types  = '';

if ($nama !== null) {
    $fields[] = 'nama = ?';
    $params[] = $nama;
    $types .= 's';
}
if ($email !== null) {
    $fields[] = 'email = ?';
    $params[] = $email;
    $types .= 's';
}
if ($phone !== null) {
    $fields[] = 'phone = ?';
    $params[] = $phone;
    $types .= 's';
}
if ($alamat !== null) {
    $fields[] = 'alamat = ?';
    $params[] = $alamat;
    $types .= 's';
}
if ($tanggal_lahir !== null) {
    $fields[] = 'tanggal_lahir = ?';
    $params[] = $tanggal_lahir;
    $types .= 's';
}
if ($jenis_kelamin !== null) {
    $fields[] = 'jenis_kelamin = ?';
    $params[] = $jenis_kelamin;
    $types .= 's';
}
if ($jabatan !== null) {
    $fields[] = 'jabatan = ?';
    $params[] = $jabatan;
    $types .= 's';
}
if ($departemen !== null) {
    $fields[] = 'departemen = ?';
    $params[] = $departemen;
    $types .= 's';
}
if ($gaji !== null) {
    $fields[] = 'gaji = ?';
    $params[] = $gaji;
    $types .= 'i';
}
if ($tanggal_masuk !== null) {
    $fields[] = 'tanggal_masuk = ?';
    $params[] = $tanggal_masuk;
    $types .= 's';
}
if ($status_karyawan !== null) {
    $fields[] = 'status_karyawan = ?';
    $params[] = $status_karyawan;
    $types .= 's';
}
if ($foto_url !== null) {
    $fields[] = 'foto_url = ?';
    $params[] = $foto_url;
    $types .= 's';
}

if (count($fields) === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tidak ada field yang diperbarui'
    ]);
    exit;
}

$sql = 'UPDATE pegawai SET ' . implode(', ', $fields) . ' WHERE id = ?';
$stmt = $conn->prepare($sql);
if ($stmt) {
    $types .= 'i';
    $params[] = $id;

    // bind_param membutuhkan argumen by reference
    $bindParams = [];
    $bindParams[] = &$types;
    foreach ($params as $k => $v) {
        $bindParams[] = &$params[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $bindParams);

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
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}
