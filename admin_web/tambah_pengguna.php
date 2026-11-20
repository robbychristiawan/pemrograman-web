<?php
require_once("config.php");
cek_login();

$page_title = "Tambah Pengguna Baru";
$current_page = "tambah_pengguna";

$error = "";

if (isset($_POST['Submit'])) {
    $username     = trim(mysqli_real_escape_string($mysqli, $_POST['username'] ?? ''));
    $nama_lengkap = trim(mysqli_real_escape_string($mysqli, $_POST['nama_lengkap'] ?? ''));
    $email        = trim(mysqli_real_escape_string($mysqli, $_POST['email'] ?? ''));
    $role         = trim(mysqli_real_escape_string($mysqli, $_POST['role'] ?? 'petugas'));
    $password     = trim($_POST['password'] ?? '');

    if (empty($username) || empty($nama_lengkap) || empty($password)) {
        $error = "Username, Nama Lengkap, dan Password wajib diisi!";
    } else {
        // Cek apakah username sudah dipakai
        $cek_user = mysqli_query($mysqli, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $error = "Username sudah digunakan, silakan pilih username lain!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users(username, password, nama_lengkap, email, role) VALUES('$username', '$hashed_password', '$nama_lengkap', '$email', '$role')";
            $result = mysqli_query($mysqli, $query);

            if ($result) {
                header("Location: pengguna.php?pesan=tambah_sukses");
                exit;
            } else {
                $error = "Gagal menyimpan pengguna: " . mysqli_error($mysqli);
            }
        }
    }
}

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
                    <h2>Tambah Data Pengguna Baru</h2>
                    <a href="pengguna.php" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar Pengguna</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-user-plus mr-2"></i> Formulir Pengguna Baru</h2>
                    </div>

                    <div class="padding_infor_info">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?= htmlspecialchars($error); ?>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        <?php endif; ?>

                        <form action="tambah_pengguna.php" method="POST">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Username <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="username" class="form-control" placeholder="Contoh: petugas1" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Ahmad Fauzi" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" placeholder="Contoh: ahmad@gmail.com" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Peran (Role) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="role" class="form-control">
                                        <option value="petugas">Petugas</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Password <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan password untuk user ini" required />
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" name="Submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                                        <i class="fa fa-save mr-1"></i> Simpan Pengguna
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
