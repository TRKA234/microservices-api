<?php
header('Content-Type: application/json');

$uri = $_SERVER['REQUEST_URI'];

// Basic CORS for root route and routing layer
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (strpos($uri, '/auth/register') !== false) {
    require_once __DIR__ . '/auth/register.php';
} else if (strpos($uri, '/auth/login') !== false) {
    require_once __DIR__ . '/auth/login.php';
} else if (strpos($uri, '/pegawai/add') !== false) {
    require_once __DIR__ . '/pegawai/add.php';
} else if (strpos($uri, '/pegawai/list') !== false) {
    require_once __DIR__ . '/pegawai/list.php';
} else if (strpos($uri, '/pegawai/edit') !== false) {
    require_once __DIR__ . '/pegawai/edit.php';
} else if (strpos($uri, '/pegawai/delete') !== false) {
    require_once __DIR__ . '/pegawai/delete.php';
} else {
    echo json_encode(["status" => "API berjalan"]);
}
