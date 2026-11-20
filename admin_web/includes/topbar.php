<?php
// includes/topbar.php
$user_nama = $_SESSION['nama_lengkap'] ?? 'Administrator';
$user_foto = $_SESSION['foto'] ?? '';
$avatar_path = (!empty($user_foto) && file_exists('uploads/profil/' . $user_foto))
    ? 'uploads/profil/' . htmlspecialchars($user_foto)
    : 'images/layout_img/user_img.jpg';
?>
<!-- right content -->
<div id="content">
    <!-- topbar -->
    <div class="topbar">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="full">
                <button type="button" id="sidebarCollapse" class="sidebar_toggle"><i class="fa fa-bars"></i></button>
                <div class="logo_section">
                    <a href="index.php"><span class="text-white font-weight-bold ml-3" style="font-size: 20px; line-height: 60px;">SIMRS PANEL</span></a>
                </div>
                <div class="right_topbar">
                    <div class="icon_info">
                        <ul class="user_profile_dd">
                            <li>
                                <a class="dropdown-toggle" data-toggle="dropdown">
                                    <img class="img-responsive rounded-circle" style="width: 35px; height: 35px; object-fit: cover;" src="<?= $avatar_path; ?>" alt="Profile" />
                                    <span class="name_user"><?= htmlspecialchars($user_nama); ?></span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="pengguna.php"><i class="fa fa-user mr-2"></i> Pengguna</a>
                                    <a class="dropdown-item" href="laporan.php"><i class="fa fa-file mr-2"></i> Laporan</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modalLogout">
                                        <i class="fa fa-sign-out mr-2"></i> <span>Log Out</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- end topbar -->
