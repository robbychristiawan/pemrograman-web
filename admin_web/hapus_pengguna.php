<?php
require_once("config.php");
cek_login();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Mencegah user menghapus akunnya sendiri
    if ($id == $_SESSION['user_id']) {
        header("Location: pengguna.php?pesan=gagal_hapus_diri");
        exit;
    }

    // Ambil info foto untuk dihapus dari server
    $res = mysqli_query($mysqli, "SELECT foto FROM users WHERE id=$id");
    if ($res && mysqli_num_rows($res) > 0) {
        $u = mysqli_fetch_assoc($res);
        if (!empty($u['foto']) && file_exists('uploads/profil/' . $u['foto'])) {
            @unlink('uploads/profil/' . $u['foto']);
        }
    }

    $result = mysqli_query($mysqli, "DELETE FROM users WHERE id=$id");
}

header("Location: pengguna.php?pesan=hapus_sukses");
exit;
?>
