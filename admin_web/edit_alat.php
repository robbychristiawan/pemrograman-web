<?php
require_once("config.php");
cek_login();

$page_title = "Edit Data Alat";
$current_page = "edit_alat";

$error = "";

// Handle Update POST
if (isset($_POST['update'])) {
    $id        = (int)$_POST['id'];
    $nama_alat = trim(mysqli_real_escape_string($mysqli, $_POST['nama_alat'] ?? ''));
    $tahun     = trim(mysqli_real_escape_string($mysqli, $_POST['tahun'] ?? ''));
    $merek     = trim(mysqli_real_escape_string($mysqli, $_POST['merek'] ?? ''));
    $lokasi    = trim(mysqli_real_escape_string($mysqli, $_POST['lokasi'] ?? ''));

    if (empty($nama_alat) || empty($tahun) || empty($merek) || empty($lokasi)) {
        $error = "Semua bidang formulir wajib diisi!";
    } else {
        $update_query = "UPDATE alat SET nama_alat='$nama_alat', tahun='$tahun', merek='$merek', lokasi='$lokasi' WHERE id=$id";
        $update_result = mysqli_query($mysqli, $update_query);

        if ($update_result) {
            header("Location: alat.php?pesan=edit_sukses");
            exit;
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($mysqli);
        }
    }
}

// Ambil data berdasarkan ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: alat.php");
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM alat WHERE id=$id";
$result = mysqli_query($mysqli, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: alat.php");
    exit;
}

$data = mysqli_fetch_assoc($result);

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
                    <h2>Edit Data Alat Elektromedis</h2>
                    <a href="alat.php" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar Alat</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-pencil-square-o mr-2"></i> Perbarui Data: <?= htmlspecialchars($data['nama_alat']); ?></h2>
                    </div>

                    <div class="padding_infor_info">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?= htmlspecialchars($error); ?>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        <?php endif; ?>

                        <form action="edit_alat.php" method="POST">
                            <input type="hidden" name="id" value="<?= $data['id']; ?>" />

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Nama Alat <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="nama_alat" value="<?= htmlspecialchars($data['nama_alat']); ?>" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Tahun Pengadaan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="tahun" min="1990" max="<?= date('Y') + 1; ?>" value="<?= htmlspecialchars($data['tahun']); ?>" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Merek Alat <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="merek" value="<?= htmlspecialchars($data['merek']); ?>" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Lokasi / Ruangan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="lokasi" value="<?= htmlspecialchars($data['lokasi']); ?>" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" name="update" class="btn btn-warning text-white px-4 py-2 font-weight-bold">
                                        <i class="fa fa-save mr-1"></i> Simpan Perubahan
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
