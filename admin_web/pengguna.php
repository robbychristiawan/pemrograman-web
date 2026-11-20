<?php
require_once("config.php");
cek_login();

$page_title = "Manajemen Pengguna";
$current_page = "pengguna";

$pesan = $_GET['pesan'] ?? '';
$error = '';

$upload_dir = 'uploads/profil/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fungsi bantu untuk validasi dan upload foto
function proses_upload_foto($file_input, &$error_msg) {
    global $upload_dir;
    if (!isset($_FILES[$file_input]) || $_FILES[$file_input]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Tidak ada file yang diunggah
    }

    $file = $_FILES[$file_input];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Terjadi kegagalan saat mengunggah foto!";
        return false;
    }

    // Validasi ukuran (Maks 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        $error_msg = "Ukuran file foto maksimal adalah 2 MB!";
        return false;
    }

    // Validasi ekstensi dan tipe file
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed_ext)) {
        $error_msg = "Format foto hanya boleh JPG, JPEG, PNG, atau WEBP!";
        return false;
    }

    // Buat nama file unik
    $nama_foto_baru = 'user_' . time() . '_' . rand(100, 999) . '.' . $ext;
    $target_file = $upload_dir . $nama_foto_baru;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $nama_foto_baru;
    } else {
        $error_msg = "Gagal memindahkan file foto ke direktori server!";
        return false;
    }
}

// Handle POST Aksi (Tambah & Edit Pengguna) via Modal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $username     = trim(mysqli_real_escape_string($mysqli, $_POST['username'] ?? ''));
        $nama_lengkap = trim(mysqli_real_escape_string($mysqli, $_POST['nama_lengkap'] ?? ''));
        $email        = trim(mysqli_real_escape_string($mysqli, $_POST['email'] ?? ''));
        $role         = trim(mysqli_real_escape_string($mysqli, $_POST['role'] ?? 'petugas'));
        $password     = trim($_POST['password'] ?? '');

        if (empty($username) || empty($nama_lengkap) || empty($password)) {
            $error = "Username, Nama Lengkap, dan Password wajib diisi!";
        } else {
            $cek = mysqli_query($mysqli, "SELECT id FROM users WHERE username = '$username'");
            if (mysqli_num_rows($cek) > 0) {
                $error = "Username '$username' sudah terdaftar, silakan gunakan username lain!";
            } else {
                // Proses upload foto jika ada
                $foto_nama = proses_upload_foto('foto', $error);
                if ($foto_nama === false) {
                    // Ada error saat upload, pesan sudah tersimpan di $error
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $foto_db = $foto_nama ? "'$foto_nama'" : "NULL";
                    $query = "INSERT INTO users(username, password, nama_lengkap, email, role, foto) 
                              VALUES('$username', '$hashed_password', '$nama_lengkap', '$email', '$role', $foto_db)";
                    if (mysqli_query($mysqli, $query)) {
                        header("Location: pengguna.php?pesan=tambah_sukses");
                        exit;
                    } else {
                        $error = "Gagal menyimpan pengguna: " . mysqli_error($mysqli);
                    }
                }
            }
        }
    } elseif ($aksi === 'edit') {
        $id           = (int)($_POST['id'] ?? 0);
        $username     = trim(mysqli_real_escape_string($mysqli, $_POST['username'] ?? ''));
        $nama_lengkap = trim(mysqli_real_escape_string($mysqli, $_POST['nama_lengkap'] ?? ''));
        $email        = trim(mysqli_real_escape_string($mysqli, $_POST['email'] ?? ''));
        $role         = trim(mysqli_real_escape_string($mysqli, $_POST['role'] ?? 'petugas'));
        $password     = trim($_POST['password'] ?? '');

        if (empty($username) || empty($nama_lengkap) || $id === 0) {
            $error = "Data pengguna tidak lengkap!";
        } else {
            $cek = mysqli_query($mysqli, "SELECT id, foto FROM users WHERE username = '$username' AND id != $id");
            if (mysqli_num_rows($cek) > 0) {
                $error = "Username '$username' sudah digunakan oleh pengguna lain!";
            } else {
                // Ambil data user lama untuk cek foto lama
                $user_lama_res = mysqli_query($mysqli, "SELECT foto FROM users WHERE id=$id");
                $user_lama = mysqli_fetch_assoc($user_lama_res);
                $foto_lama = $user_lama['foto'] ?? '';

                // Cek apakah ada file foto baru yang diunggah
                $foto_baru = proses_upload_foto('foto', $error);
                if ($foto_baru === false) {
                    // Ada error upload foto
                } else {
                    $sql_foto_part = "";
                    if (!empty($foto_baru)) {
                        // Hapus file foto lama jika ada
                        if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                            @unlink($upload_dir . $foto_lama);
                        }
                        $sql_foto_part = ", foto='$foto_baru'";
                    }

                    if (!empty($password)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $query = "UPDATE users SET username='$username', password='$hashed_password', nama_lengkap='$nama_lengkap', email='$email', role='$role' $sql_foto_part WHERE id=$id";
                    } else {
                        $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', email='$email', role='$role' $sql_foto_part WHERE id=$id";
                    }

                    if (mysqli_query($mysqli, $query)) {
                        // Update sesi jika yang diedit adalah user yang sedang login
                        if ($id == $_SESSION['user_id']) {
                            $_SESSION['username']     = $username;
                            $_SESSION['nama_lengkap'] = $nama_lengkap;
                            $_SESSION['role']         = $role;
                            if (!empty($foto_baru)) {
                                $_SESSION['foto'] = $foto_baru;
                            }
                        }
                        header("Location: pengguna.php?pesan=edit_sukses");
                        exit;
                    } else {
                        $error = "Gagal memperbarui pengguna: " . mysqli_error($mysqli);
                    }
                }
            }
        }
    }
}

$cari = isset($_GET['cari']) ? trim(mysqli_real_escape_string($mysqli, $_GET['cari'])) : '';

if (!empty($cari)) {
    $query = "SELECT * FROM users WHERE username LIKE '%$cari%' OR nama_lengkap LIKE '%$cari%' OR email LIKE '%$cari%' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM users ORDER BY id DESC";
}

$result = mysqli_query($mysqli, $query);

include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/topbar.php");
?>

<div class="midde_cont">
    <div class="container-fluid">
        <!-- Title bar -->
        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title d-flex justify-content-between align-items-center flex-wrap">
                    <h2>Manajemen Data Pengguna Sistem</h2>
                    <!-- Tombol Tambah Pengguna -->
                    <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#modalTambahPengguna">
                        <i class="fa fa-user-plus mr-1"></i> Tambah Pengguna Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert pesan -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle mr-1"></i> <?= htmlspecialchars($error); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($pesan === 'tambah_sukses'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> Pengguna baru berhasil ditambahkan!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php elseif ($pesan === 'edit_sukses'): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> Data pengguna dan foto profil berhasil diperbarui!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php elseif ($pesan === 'hapus_sukses'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> Pengguna berhasil dihapus!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php elseif ($pesan === 'gagal_hapus_diri'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle mr-1"></i> Anda tidak dapat menghapus akun yang sedang Anda gunakan!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Table Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head d-flex justify-content-between align-items-center flex-wrap">
                        <h2><i class="fa fa-users mr-2"></i> Daftar Akun Pengguna (Total: <?= mysqli_num_rows($result); ?>)</h2>
                        <form action="pengguna.php" method="GET" class="form-inline mt-2 mt-md-0">
                            <div class="input-group">
                                <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama / username..." value="<?= htmlspecialchars($cari); ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                    <?php if (!empty($cari)): ?>
                                        <a href="pengguna.php" class="btn btn-sm btn-secondary" title="Reset"><i class="fa fa-refresh"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table_section padding_infor_info">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 70px;" class="text-center">Foto</th>
                                        <th>Username</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Peran (Role)</th>
                                        <th>Waktu Dibuat</th>
                                        <th class="text-center" style="width: 170px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    if (mysqli_num_rows($result) > 0):
                                        while ($u = mysqli_fetch_assoc($result)): 
                                            $foto_path = (!empty($u['foto']) && file_exists('uploads/profil/' . $u['foto']))
                                                ? 'uploads/profil/' . htmlspecialchars($u['foto'])
                                                : 'images/layout_img/user_img.jpg';
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td class="text-center">
                                                <img src="<?= $foto_path; ?>" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover; border: 2px solid #e9ecef;" alt="Foto" />
                                            </td>
                                            <td><strong><?= htmlspecialchars($u['username']); ?></strong></td>
                                            <td><?= htmlspecialchars($u['nama_lengkap']); ?></td>
                                            <td><?= htmlspecialchars($u['email'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge badge-<?= $u['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                                    <?= ucfirst(htmlspecialchars($u['role'])); ?>
                                                </span>
                                            </td>
                                            <td><small class="text-muted"><?= htmlspecialchars($u['created_at']); ?></small></td>
                                            <td class="text-center">
                                                <!-- Tombol Edit Pop-up Modal -->
                                                <button type="button" class="btn btn-sm btn-warning text-white btn-edit-user"
                                                        data-toggle="modal"
                                                        data-target="#modalEditPengguna"
                                                        data-id="<?= $u['id']; ?>"
                                                        data-username="<?= htmlspecialchars($u['username']); ?>"
                                                        data-nama="<?= htmlspecialchars($u['nama_lengkap']); ?>"
                                                        data-email="<?= htmlspecialchars($u['email'] ?? ''); ?>"
                                                        data-role="<?= htmlspecialchars($u['role']); ?>"
                                                        data-foto="<?= htmlspecialchars($u['foto'] ?? ''); ?>"
                                                        title="Edit Pengguna & Foto">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>

                                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                                    <!-- Tombol Hapus Pop-up Modal -->
                                                    <button type="button" class="btn btn-sm btn-danger btn-hapus-user"
                                                            data-toggle="modal"
                                                            data-target="#modalHapusPengguna"
                                                            data-id="<?= $u['id']; ?>"
                                                            data-username="<?= htmlspecialchars($u['username']); ?>"
                                                            title="Hapus Pengguna">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Akun Anda Saat Ini"><i class="fa fa-lock"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- 1. MODAL POP-UP TAMBAH PENGGUNA                                -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTambahPengguna" tabindex="-1" role="dialog" aria-labelledby="modalTambahPenggunaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalTambahPenggunaLabel">
                    <i class="fa fa-user-plus mr-2"></i> Tambah Pengguna Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="pengguna.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="tambah">
                    
                    <div class="form-group text-center mb-3">
                        <label class="font-weight-bold d-block">Foto Profil (Opsional)</label>
                        <img id="tambah_preview_foto" src="images/layout_img/user_img.jpg" class="rounded-circle shadow-sm mb-2" style="width: 75px; height: 75px; object-fit: cover; border: 2px solid #007bff;" alt="Preview" />
                        <div class="custom-file text-left mt-1">
                            <input type="file" name="foto" class="custom-file-input" id="customFileTambah" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this, 'tambah_preview_foto')" />
                            <label class="custom-file-label" for="customFileTambah">Pilih Foto...</label>
                        </div>
                        <small class="form-text text-muted">Format: JPG, PNG, WEBP (Maks 2MB)</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: petugas1" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Ahmad Fauzi" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Contoh: ahmad@gmail.com" />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Peran (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-control">
                            <option value="petugas">Petugas</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password untuk akun ini" required />
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                        <i class="fa fa-save mr-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. MODAL POP-UP EDIT PENGGUNA                                  -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditPengguna" tabindex="-1" role="dialog" aria-labelledby="modalEditPenggunaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalEditPenggunaLabel">
                    <i class="fa fa-pencil-square-o mr-2"></i> Edit Data & Foto Pengguna
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="pengguna.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="id" id="edit_user_id">
                    
                    <!-- Bagian Foto Profil -->
                    <div class="form-group text-center mb-3">
                        <label class="font-weight-bold d-block">Foto Profil</label>
                        <img id="edit_foto_preview" src="images/layout_img/user_img.jpg" class="rounded-circle shadow-sm mb-2" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #ffc107;" alt="Preview" />
                        <div class="custom-file text-left mt-1">
                            <input type="file" name="foto" class="custom-file-input" id="customFileEdit" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this, 'edit_foto_preview')" />
                            <label class="custom-file-label" for="customFileEdit">Ganti Foto Profil...</label>
                        </div>
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto saat ini.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="edit_user_username" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" id="edit_user_nama" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" id="edit_user_email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Peran (Role) <span class="text-danger">*</span></label>
                        <select name="role" id="edit_user_role" class="form-control">
                            <option value="petugas">Petugas</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" />
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white font-weight-bold px-4">
                        <i class="fa fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 3. MODAL POP-UP KONFIRMASI HAPUS PENGGUNA                      -->
<!-- ============================================================== -->
<div class="modal fade" id="modalHapusPengguna" tabindex="-1" role="dialog" aria-labelledby="modalHapusPenggunaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalHapusPenggunaLabel">
                    <i class="fa fa-trash mr-2"></i> Konfirmasi Hapus Pengguna
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h6 class="font-weight-bold">Apakah Anda yakin ingin menghapus akun pengguna ini?</h6>
                <p class="text-muted mb-0">Username: <span id="hapus_user_text" class="text-danger font-weight-bold"></span></p>
                <small class="text-muted d-block mt-2">Akun dan foto profil terkait akan dihapus secara permanen.</small>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                <a href="#" id="btnKonfirmasiHapusUser" class="btn btn-danger font-weight-bold px-4">
                    <i class="fa fa-trash mr-1"></i> Ya, Hapus Pengguna
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi preview gambar sebelum submit
function previewImage(input, targetImgId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetImgId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);

        // Update custom file label text
        var fileName = input.files[0].name;
        var label = input.nextElementSibling;
        if (label) {
            label.innerText = fileName;
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // Populate Modal Edit Pengguna
    $('#modalEditPengguna').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#edit_user_id').val(button.data('id'));
        modal.find('#edit_user_username').val(button.data('username'));
        modal.find('#edit_user_nama').val(button.data('nama'));
        modal.find('#edit_user_email').val(button.data('email'));
        modal.find('#edit_user_role').val(button.data('role'));

        var foto = button.data('foto');
        if (foto && foto !== '') {
            modal.find('#edit_foto_preview').attr('src', 'uploads/profil/' + foto);
        } else {
            modal.find('#edit_foto_preview').attr('src', 'images/layout_img/user_img.jpg');
        }
        modal.find('#customFileEdit').val('');
        modal.find("label[for='customFileEdit']").text('Ganti Foto Profil...');
    });

    // Reset Modal Tambah saat ditutup
    $('#modalTambahPengguna').on('hidden.bs.modal', function () {
        $(this).find('#tambah_preview_foto').attr('src', 'images/layout_img/user_img.jpg');
        $(this).find("label[for='customFileTambah']").text('Pilih Foto...');
    });

    // Populate Modal Hapus Pengguna
    $('#modalHapusPengguna').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var username = button.data('username');
        var modal = $(this);
        modal.find('#hapus_user_text').text(username);
        modal.find('#btnKonfirmasiHapusUser').attr('href', 'hapus_pengguna.php?id=' + id);
    });

    // Otomatis buka modal tambah jika ?modal=tambah
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('modal') === 'tambah') {
        $('#modalTambahPengguna').modal('show');
    }
});
</script>

<?php include_once("includes/footer.php"); ?>
