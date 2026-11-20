<?php
require_once("config.php");
cek_login();

$redirect = $_GET['redirect'] ?? 'alat.php';

// Validasi redirect agar aman
if (strpos($redirect, 'index.php') !== false) {
    $target = "index.php?pesan=hapus_sukses";
} else {
    $target = "alat.php?pesan=hapus_sukses";
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($mysqli, "DELETE FROM alat WHERE id=$id");
}

header("Location: " . $target);
exit;
?>
