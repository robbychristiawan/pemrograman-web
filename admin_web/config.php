<?php
// config.php - Konfigurasi Database dan Sesi

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$databaseHost = 'localhost';
$databaseName = 'simrs';
$databaseUsername = 'root';
$databasePassword = '';

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if (!$mysqli) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Fungsi untuk memeriksa apakah user sudah login
function cek_login() {
    global $mysqli;
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header("Location: login.php?pesan=belum_login");
        exit;
    }

    // Pastikan foto profil selalu tersinkronisasi dengan database
    if (isset($_SESSION['user_id']) && !isset($_SESSION['foto'])) {
        $uid = (int)$_SESSION['user_id'];
        $res = mysqli_query($mysqli, "SELECT foto FROM users WHERE id=$uid");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $_SESSION['foto'] = $row['foto'];
        }
    }
}
?>
