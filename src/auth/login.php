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

if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$email = $data->email;
$password = $data->password;

$stmt = $conn->prepare("SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Server error']);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Password salah']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan']);
}
