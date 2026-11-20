<?php
require_once("config.php");
cek_login();

$page_title = "Tambah Alat Baru";
$current_page = "tambah_alat";

$error = "";

if (isset($_POST['Submit'])) {
    $nama_alat = trim(mysqli_real_escape_string($mysqli, $_POST['nama_alat'] ?? ''));
    $tahun     = trim(mysqli_real_escape_string($mysqli, $_POST['tahun'] ?? ''));
    $merek     = trim(mysqli_real_escape_string($mysqli, $_POST['merek'] ?? ''));
    $lokasi    = trim(mysqli_real_escape_string($mysqli, $_POST['lokasi'] ?? ''));

    if (empty($nama_alat) || empty($tahun) || empty($merek) || empty($lokasi)) {
        $error = "Semua bidang formulir wajib diisi!";
    } else {
        $query = "INSERT INTO alat(nama_alat, tahun, merek, lokasi) VALUES('$nama_alat', '$tahun', '$merek', '$lokasi')";
        $result = mysqli_query($mysqli, $query);

        if ($result) {
            header("Location: alat.php?pesan=tambah_sukses");
            exit;
        } else {
            $error = "Terjadi kesalahan saat menyimpan data: " . mysqli_error($mysqli);
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
                    <h2>Tambah Data Alat Elektromedis</h2>
                    <a href="alat.php" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar Alat</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-plus-square mr-2"></i> Formulir Tambah Alat</h2>
                    </div>

                    <div class="padding_infor_info">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?= htmlspecialchars($error); ?>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        <?php endif; ?>

                        <form action="tambah_alat.php" method="POST">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Nama Alat <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Patient Monitor, USG, Ventilator" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Tahun Pengadaan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="tahun" min="1990" max="<?= date('Y') + 1; ?>" value="<?= date('Y'); ?>" class="form-control" placeholder="Contoh: 2022" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Merek Alat <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="merek" class="form-control" placeholder="Contoh: GE Healthcare, Mindray, Philips" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Lokasi / Ruangan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="lokasi" class="form-control" placeholder="Contoh: R. ICU, R. IGD, R. Radiologi" required />
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" name="Submit" class="btn btn-success px-4 py-2 font-weight-bold">
                                        <i class="fa fa-save mr-1"></i> Simpan Alat
                                    </button>
                                    <a href="alat.php" class="btn btn-light border px-4 py-2 ml-2">Batal</a>
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
