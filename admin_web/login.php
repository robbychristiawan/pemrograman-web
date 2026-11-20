<?php
require_once("config.php");

// Jika sudah login, langsung arahkan ke index.php
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: index.php");
    exit;
}

$pesan_error = "";
$pesan_sukses = "";

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == "logout") {
        $pesan_sukses = "Anda telah berhasil keluar (logout).";
    } elseif ($_GET['pesan'] == "belum_login") {
        $pesan_error = "Silakan login terlebih dahulu untuk mengakses sistem.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($mysqli, $_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $pesan_error = "Username dan Password wajib diisi!";
    } else {
        // Cari user berdasarkan username atau email
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username' LIMIT 1";
        $result = mysqli_query($mysqli, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password (mendukung password_hash dan plaintext fallback)
            $valid_password = false;
            if (password_verify($password, $user['password'])) {
                $valid_password = true;
            } elseif ($password === $user['password'] || md5($password) === $user['password']) {
                $valid_password = true;
            }

            if ($valid_password) {
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['foto'] = $user['foto'];

                header("Location: index.php");
                exit;
            } else {
                $pesan_error = "Password yang Anda masukkan salah!";
            }
        } else {
            $pesan_error = "Username tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Login - Panel Admin SIMRS</title>
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/responsive.css" />
    <link rel="stylesheet" href="css/colors.css" />
    <link rel="stylesheet" href="css/custom.css" />
</head>
<body class="inner_page login">
    <div class="full_container">
        <div class="container">
            <div class="center verticle_center full_height">
                <div class="login_section">
                    <div class="logo_login text-center pt-4" style="position: relative; z-index: 10;">
                        <h1 class="font-weight-bold mb-2" style="color: #ffffff !important; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.9);">Sistem Manajemen Data & Alat Medis</h1>
                        <h4 class="font-weight-bold" style="color: #ffffff !important; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.9);">SIMRS ADMIN</h4>
                    </div>
                    <div class="login_form">
                        <?php if (!empty($pesan_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Gagal!</strong> <?= htmlspecialchars($pesan_error); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pesan_sukses)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan_sukses); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <fieldset>
                                <div class="field">
                                    <label class="label_field">Username / Email</label>
                                    <input type="text" name="username" placeholder="Masukkan username atau email" required autofocus />
                                </div>
                                <div class="field">
                                    <label class="label_field">Password</label>
                                    <input type="password" name="password" placeholder="Masukkan password" required />
                                </div>
                                <div class="field">
                                    <label class="label_field hidden">hidden</label>
                                    <label class="form-check-label text-muted">
                                        <small>Default Akun: <b>admin</b> / <b>admin123</b></small>
                                    </label>
                                </div>
                                <div class="field margin_0">
                                    <label class="label_field hidden">hidden</label>
                                    <button type="submit" class="main_bt w-100">Masuk ke Sistem</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
