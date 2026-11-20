<?php
require_once("config.php");
cek_login();

$page_title = "Edit Data Pengguna";
$current_page = "edit_pengguna";

$error = "";

// Handle Update POST
if (isset($_POST['update'])) {
    $id           = (int)$_POST['id'];
    $username     = trim(mysqli_real_escape_string($mysqli, $_POST['username'] ?? ''));
    $nama_lengkap = trim(mysqli_real_escape_string($mysqli, $_POST['nama_lengkap'] ?? ''));
    $email        = trim(mysqli_real_escape_string($mysqli, $_POST['email'] ?? ''));
    $role         = trim(mysqli_real_escape_string($mysqli, $_POST['role'] ?? 'petugas'));
    $password     = trim($_POST['password'] ?? '');

    if (empty($username) || empty($nama_lengkap)) {
        $error = "Username dan Nama Lengkap wajib diisi!";
    } else {
        // Cek duplikasi username untuk ID lain
        $cek = mysqli_query($mysqli, "SELECT id FROM users WHERE username = '$username' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username '$username' sudah digunakan pengguna lain!";
        } else {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET username='$username', password='$hashed_password', nama_lengkap='$nama_lengkap', email='$email', role='$role' WHERE id=$id";
            } else {
                $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', email='$email', role='$role' WHERE id=$id";
            }

            $res = mysqli_query($mysqli, $query);
            if ($res) {
                // Update session jika yang diedit adalah user yang sedang login
                if ($id == $_SESSION['user_id']) {
                    $_SESSION['username'] = $username;
                    $_SESSION['nama_lengkap'] = $nama_lengkap;
                    $_SESSION['role'] = $role;
                }
                header("Location: pengguna.php?pesan=edit_sukses");
                exit;
            } else {
                $error = "Gagal memperbarui pengguna: " . mysqli_error($mysqli);
            }
        }
    }
}

// Ambil data user
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pengguna.php");
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM users WHERE id=$id";
$result = mysqli_query($mysqli, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: pengguna.php");
    exit;
}

$u = mysqli_fetch_assoc($result);

include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/topbar.php");
?>

<div class="midde_cont">
    <div class="container-fluid">
        <!-- Title bar -->
        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title d-flex justify-content-between align-items-center">
                    <h2>Edit Data Pengguna</h2>
                    <a href="pengguna.php" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar Pengguna</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-user-circle mr-2"></i> Edit Akun: <?= htmlspecialchars($u['username']); ?></h2>
                    </div>

                    <div class="padding_infor_info">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?= htmlspecialchars($error); ?>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        <?php endif; ?>

                        <form action="edit_pengguna.php" method="POST">
                            <input type="hidden" name="id" value="<?= $u['id']; ?>" />

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Username <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="username" value="<?= htmlspecialchars($u['username']); ?>" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($u['nama_lengkap']); ?>" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" value="<?= htmlspecialchars($u['email'] ?? ''); ?>" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Peran (Role) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="role" class="form-control">
                                        <option value="petugas" <?= ($u['role'] === 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                        <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Password Baru</label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" />
                                    <small class="form-text text-muted">Hanya isi bidang ini jika Anda ingin mengganti password user.</small>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" name="update" class="btn btn-warning text-white px-4 py-2 font-weight-bold">
                                        <i class="fa fa-save mr-1"></i> Simpan Perubahan
                                    </button>
                                    <a href="pengguna.php" class="btn btn-light border px-4 py-2 ml-2">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include_once("includes/footer.php"); ?>
