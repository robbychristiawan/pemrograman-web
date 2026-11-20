<?php
// includes/sidebar.php
if (!isset($current_page)) {
    $current_page = 'dashboard';
}

$user_nama = $_SESSION['nama_lengkap'] ?? 'Administrator';
$user_role = $_SESSION['role'] ?? 'admin';
$user_foto = $_SESSION['foto'] ?? '';
$avatar_path = (!empty($user_foto) && file_exists('uploads/profil/' . $user_foto))
    ? 'uploads/profil/' . htmlspecialchars($user_foto)
    : 'images/layout_img/user_img.jpg';
?>
<!-- Sidebar  -->
<nav id="sidebar">
    <div class="sidebar_blog_1">
        <div class="sidebar-header">
            <div class="logo_section">
                <a href="index.php"><img class="logo_icon img-responsive" src="images/logo/logoTEM.jpg" alt="Logo" /></a>
            </div>
        </div>
        <div class="sidebar_user_info">
            <div class="icon_setting"></div>
            <div class="user_profle_side">
                <div class="user_img"><img class="img-responsive rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" src="<?= $avatar_path; ?>" alt="User" /></div>
                <div class="user_info">
                    <h6><?= htmlspecialchars($user_nama); ?></h6>
                    <p><span class="online_animation"></span> <?= ucfirst(htmlspecialchars($user_role)); ?> Online</p>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar_blog_2">
        <h4>Menu Utama</h4>
        <ul class="list-unstyled components">
            <li class="<?= ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <a href="index.php"><i class="fa fa-dashboard yellow_color"></i> <span>Dashboard</span></a>
            </li>
            
            <li class="<?= in_array($current_page, ['alat', 'tambah_alat', 'edit_alat']) ? 'active' : ''; ?>">
                <a href="#menuAlat" data-toggle="collapse" aria-expanded="<?= in_array($current_page, ['alat', 'tambah_alat', 'edit_alat']) ? 'true' : 'false'; ?>" class="dropdown-toggle">
                    <i class="fa fa-cubes blue1_color"></i> <span>Data Produk / Alat</span>
                </a>
                <ul class="collapse list-unstyled <?= in_array($current_page, ['alat', 'tambah_alat', 'edit_alat']) ? 'show' : ''; ?>" id="menuAlat">
                    <li><a href="alat.php">> <span>Daftar Alat</span></a></li>
                    <li><a href="alat.php?modal=tambah">> <span>+ Tambah Alat</span></a></li>
                </ul>
            </li>

            <li class="<?= in_array($current_page, ['pengguna', 'tambah_pengguna', 'edit_pengguna']) ? 'active' : ''; ?>">
                <a href="#menuPengguna" data-toggle="collapse" aria-expanded="<?= in_array($current_page, ['pengguna', 'tambah_pengguna', 'edit_pengguna']) ? 'true' : 'false'; ?>" class="dropdown-toggle">
                    <i class="fa fa-users purple_color2"></i> <span>Manajemen Pengguna</span>
                </a>
                <ul class="collapse list-unstyled <?= in_array($current_page, ['pengguna', 'tambah_pengguna', 'edit_pengguna']) ? 'show' : ''; ?>" id="menuPengguna">
                    <li><a href="pengguna.php">> <span>Daftar Pengguna</span></a></li>
                    <li><a href="pengguna.php?modal=tambah">> <span>+ Tambah Pengguna</span></a></li>
                </ul>
            </li>

            <li class="<?= ($current_page == 'laporan') ? 'active' : ''; ?>">
                <a href="laporan.php"><i class="fa fa-file-text-o green_color"></i> <span>Laporan & Cetak</span></a>
            </li>

            <li>
                <a href="#" data-toggle="modal" data-target="#modalLogout">
                    <i class="fa fa-sign-out red_color"></i> <span>Keluar / Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
<!-- end sidebar -->
