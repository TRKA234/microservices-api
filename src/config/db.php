<?php
$host = "db";
$user = "root";
$pass = "root123";
$db   = "db_pegawai";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
