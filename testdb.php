<?php
$servername = "172.16.85.81"; // Ganti dengan alamat server database Anda
$username = "user_inlislite"; // Ganti dengan username database Anda
$password = "password_inlislite"; // Ganti dengan password database Anda
$dbname = "dbsirkulasi"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Tutup koneksi
$conn->close();
?>
