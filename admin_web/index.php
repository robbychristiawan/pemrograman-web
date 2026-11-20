<?php
require_once("config.php");
cek_login();

$page_title = "Dashboard Statistik";
$current_page = "dashboard";

$pesan = $_GET['pesan'] ?? '';
$error = '';

// Handle POST Aksi (Tambah & Edit Alat) langsung dari Dashboard
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $nama_alat = trim(mysqli_real_escape_string($mysqli, $_POST['nama_alat'] ?? ''));
        $tahun     = trim(mysqli_real_escape_string($mysqli, $_POST['tahun'] ?? ''));
        $merek     = trim(mysqli_real_escape_string($mysqli, $_POST['merek'] ?? ''));
        $lokasi    = trim(mysqli_real_escape_string($mysqli, $_POST['lokasi'] ?? ''));

        if (empty($nama_alat) || empty($tahun) || empty($merek) || empty($lokasi)) {
            $error = "Semua bidang formulir wajib diisi!";
        } else {
            $query = "INSERT INTO alat(nama_alat, tahun, merek, lokasi) VALUES('$nama_alat', '$tahun', '$merek', '$lokasi')";
            if (mysqli_query($mysqli, $query)) {
                header("Location: index.php?pesan=tambah_sukses");
                exit;
            } else {
                $error = "Gagal menyimpan data: " . mysqli_error($mysqli);
            }
        }
    } elseif ($aksi === 'edit') {
        $id        = (int)($_POST['id'] ?? 0);
        $nama_alat = trim(mysqli_real_escape_string($mysqli, $_POST['nama_alat'] ?? ''));
        $tahun     = trim(mysqli_real_escape_string($mysqli, $_POST['tahun'] ?? ''));
        $merek     = trim(mysqli_real_escape_string($mysqli, $_POST['merek'] ?? ''));
        $lokasi    = trim(mysqli_real_escape_string($mysqli, $_POST['lokasi'] ?? ''));

        if (empty($nama_alat) || empty($tahun) || empty($merek) || empty($lokasi) || $id === 0) {
            $error = "Data yang dikirim tidak lengkap!";
        } else {
            $query = "UPDATE alat SET nama_alat='$nama_alat', tahun='$tahun', merek='$merek', lokasi='$lokasi' WHERE id=$id";
            if (mysqli_query($mysqli, $query)) {
                header("Location: index.php?pesan=edit_sukses");
                exit;
            } else {
                $error = "Gagal memperbarui data: " . mysqli_error($mysqli);
            }
        }
    }
}

// Hitung Statistik
$total_alat_res = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM alat");
$total_alat = mysqli_fetch_assoc($total_alat_res)['total'] ?? 0;

$total_user_res = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM users");
$total_user = mysqli_fetch_assoc($total_user_res)['total'] ?? 0;

$total_merek_res = mysqli_query($mysqli, "SELECT COUNT(DISTINCT merek) as total FROM alat");
$total_merek = mysqli_fetch_assoc($total_merek_res)['total'] ?? 0;

$total_lokasi_res = mysqli_query($mysqli, "SELECT COUNT(DISTINCT lokasi) as total FROM alat");
$total_lokasi = mysqli_fetch_assoc($total_lokasi_res)['total'] ?? 0;

// Data untuk Chart Distribusi Lokasi
$chart_res = mysqli_query($mysqli, "SELECT lokasi, COUNT(*) as jumlah FROM alat GROUP BY lokasi");
$lokasi_labels = [];
$lokasi_data = [];
while ($row = mysqli_fetch_assoc($chart_res)) {
    $lokasi_labels[] = $row['lokasi'];
    $lokasi_data[] = (int)$row['jumlah'];
}

// 5 Data Alat Terbaru
$alat_terbaru = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC LIMIT 5");

include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/topbar.php");
?>

<!-- dashboard inner -->
<div class="midde_cont">
    <div class="container-fluid">
        <!-- Title -->
        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2>Dashboard Statistik</h2>
                        <p class="text-muted mb-0">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>!</p>
                    </div>
                    <!-- Tombol Action Pop-up Tambah Alat di Landing Page -->
                    <button type="button" class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#modalTambahAlat">
                        <i class="fa fa-plus-circle mr-1"></i> Tambah Alat Baru
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
                <i class="fa fa-check-circle mr-1"></i> Data alat berhasil ditambahkan!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php elseif ($pesan === 'edit_sukses'): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> Data alat berhasil diperbarui!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php elseif ($pesan === 'hapus_sukses'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-1"></i> Data alat berhasil dihapus!
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- 4 Counter Cards -->
        <div class="row column1">
            <div class="col-md-6 col-lg-3">
                <div class="full counter_section margin_bottom_30">
                    <div class="couter_icon">
                        <div><i class="fa fa-cubes yellow_color"></i></div>
                    </div>
                    <div class="counter_no">
                        <div>
                            <p class="total_no"><?= $total_alat; ?></p>
                            <p class="head_couter">Total Alat/Produk</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="full counter_section margin_bottom_30">
                    <div class="couter_icon">
                        <div><i class="fa fa-users blue1_color"></i></div>
                    </div>
                    <div class="counter_no">
                        <div>
                            <p class="total_no"><?= $total_user; ?></p>
                            <p class="head_couter">Total Pengguna</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="full counter_section margin_bottom_30">
                    <div class="couter_icon">
                        <div><i class="fa fa-tags green_color"></i></div>
                    </div>
                    <div class="counter_no">
                        <div>
                            <p class="total_no"><?= $total_merek; ?></p>
                            <p class="head_couter">Merek Terdaftar</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="full counter_section margin_bottom_30">
                    <div class="couter_icon">
                        <div><i class="fa fa-hospital-o red_color"></i></div>
                    </div>
                    <div class="counter_no">
                        <div>
                            <p class="total_no"><?= $total_lokasi; ?></p>
                            <p class="head_couter">Ruangan / Lokasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row: Chart & Info -->
        <div class="row column2 graph margin_bottom_30">
            <div class="col-md-7">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-bar-chart mr-2"></i> Distribusi Alat Berdasarkan Lokasi</h2>
                    </div>
                    <div class="full graph_head" style="position: relative; height: 320px;">
                        <canvas id="chartLokasi"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-info-circle mr-2"></i> Ringkasan Sistem</h2>
                    </div>
                    <div class="table_section padding_infor_info">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Database Aktif
                                <span class="badge badge-primary badge-pill">simrs</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tabel Utama
                                <span class="badge badge-info badge-pill">alat</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Login Sebagai
                                <span class="badge badge-success badge-pill"><?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Status Server
                                <span class="badge badge-warning text-dark badge-pill">Apache:8080 & MySQL Aktif</span>
                            </li>
                        </ul>
                        <div class="mt-4 text-center">
                            <a href="alat.php" class="btn btn-outline-primary btn-block"><i class="fa fa-list mr-1"></i> Kelola Seluruh Data Alat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Table with POP-UP ACTIONS -->
        <div class="row column3">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head d-flex justify-content-between align-items-center">
                        <h2><i class="fa fa-history mr-2"></i> 5 Alat Medis Terbaru</h2>
                        <a href="alat.php" class="btn btn-sm btn-info">Buka Semua Data</a>
                    </div>
                    <div class="table_section padding_infor_info">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Alat</th>
                                        <th>Tahun</th>
                                        <th>Merek</th>
                                        <th>Lokasi</th>
                                        <th class="text-center" style="width: 200px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    if (mysqli_num_rows($alat_terbaru) > 0):
                                        while ($row = mysqli_fetch_assoc($alat_terbaru)): 
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><strong><?= htmlspecialchars($row['nama_alat']); ?></strong></td>
                                            <td><span class="badge badge-secondary"><?= htmlspecialchars($row['tahun']); ?></span></td>
                                            <td><?= htmlspecialchars($row['merek']); ?></td>
                                            <td><span class="badge badge-info"><?= htmlspecialchars($row['lokasi']); ?></span></td>
                                            <td class="text-center">
                                                <!-- Action Buttons with Pop-up Modals directly on Landing Page -->
                                                <button type="button" class="btn btn-sm btn-info btn-detail-alat"
                                                        data-toggle="modal" 
                                                        data-target="#modalDetailAlat"
                                                        data-id="<?= $row['id']; ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_alat']); ?>"
                                                        data-tahun="<?= htmlspecialchars($row['tahun']); ?>"
                                                        data-merek="<?= htmlspecialchars($row['merek']); ?>"
                                                        data-lokasi="<?= htmlspecialchars($row['lokasi']); ?>"
                                                        title="Detail Alat">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning text-white btn-edit-alat"
                                                        data-toggle="modal" 
                                                        data-target="#modalEditAlat"
                                                        data-id="<?= $row['id']; ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_alat']); ?>"
                                                        data-tahun="<?= htmlspecialchars($row['tahun']); ?>"
                                                        data-merek="<?= htmlspecialchars($row['merek']); ?>"
                                                        data-lokasi="<?= htmlspecialchars($row['lokasi']); ?>"
                                                        title="Edit Data">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger btn-hapus-alat"
                                                        data-toggle="modal" 
                                                        data-target="#modalHapusAlat"
                                                        data-id="<?= $row['id']; ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_alat']); ?>"
                                                        title="Hapus Data">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile; 
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data alat.</td>
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
<!-- 1. MODAL POP-UP TAMBAH ALAT DI LANDING PAGE                    -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTambahAlat" tabindex="-1" role="dialog" aria-labelledby="modalTambahAlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalTambahAlatLabel">
                    <i class="fa fa-plus-circle mr-2"></i> Tambah Alat Elektromedis
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Alat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Infusion Pump, USG, Dental Unit" required autofocus />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Pengadaan <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" min="1990" max="<?= date('Y') + 1; ?>" value="<?= date('Y'); ?>" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Merek Alat <span class="text-danger">*</span></label>
                        <input type="text" name="merek" class="form-control" placeholder="Contoh: GE Healthcare, Terumo, Mindray" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi / Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: R. ICU, R. IGD, R. Radiologi" required />
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fa fa-save mr-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. MODAL POP-UP EDIT ALAT DI LANDING PAGE                      -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditAlat" tabindex="-1" role="dialog" aria-labelledby="modalEditAlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalEditAlatLabel">
                    <i class="fa fa-pencil-square-o mr-2"></i> Edit Data Alat Elektromedis
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Alat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_alat" id="edit_nama_alat" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Pengadaan <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="edit_tahun" min="1990" max="<?= date('Y') + 1; ?>" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Merek Alat <span class="text-danger">*</span></label>
                        <input type="text" name="merek" id="edit_merek" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi / Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" id="edit_lokasi" class="form-control" required />
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
<!-- 3. MODAL POP-UP KONFIRMASI HAPUS ALAT DI LANDING PAGE          -->
<!-- ============================================================== -->
<div class="modal fade" id="modalHapusAlat" tabindex="-1" role="dialog" aria-labelledby="modalHapusAlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalHapusAlatLabel">
                    <i class="fa fa-trash mr-2"></i> Konfirmasi Hapus Data
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h6 class="font-weight-bold">Apakah Anda yakin ingin menghapus alat ini?</h6>
                <p class="text-muted mb-0">Nama Alat: <span id="hapus_nama_text" class="text-danger font-weight-bold"></span></p>
                <small class="text-muted d-block mt-2">Tindakan ini permanen dan tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                <a href="#" id="btnKonfirmasiHapus" class="btn btn-danger font-weight-bold px-4">
                    <i class="fa fa-trash mr-1"></i> Ya, Hapus Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 4. MODAL POP-UP DETAIL ALAT DI LANDING PAGE                    -->
<!-- ============================================================== -->
<div class="modal fade" id="modalDetailAlat" tabindex="-1" role="dialog" aria-labelledby="modalDetailAlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalDetailAlatLabel">
                    <i class="fa fa-info-circle mr-2"></i> Detail Informasi Alat
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th class="bg-light" style="width: 40%;">ID Alat</th>
                        <td id="detail_id">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Alat</th>
                        <td><strong id="detail_nama_alat">-</strong></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tahun Pengadaan</th>
                        <td id="detail_tahun">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Merek</th>
                        <td id="detail_merek">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Lokasi Ruangan</th>
                        <td><span id="detail_lokasi" class="badge badge-info p-2">-</span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="js/Chart.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi Chart
    var ctx = document.getElementById("chartLokasi").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($lokasi_labels); ?>,
            datasets: [{
                label: 'Jumlah Alat',
                data: <?= json_encode($lokasi_data); ?>,
                backgroundColor: [
                    '#1ed085', '#15283c', '#ff5722', '#03a9f4',
                    '#9c27b0', '#ff9800', '#4caf50', '#e91e63'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }]
            }
        }
    });

    // Event Handler Pop-up Modals
    $('#modalEditAlat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#edit_id').val(button.data('id'));
        modal.find('#edit_nama_alat').val(button.data('nama'));
        modal.find('#edit_tahun').val(button.data('tahun'));
        modal.find('#edit_merek').val(button.data('merek'));
        modal.find('#edit_lokasi').val(button.data('lokasi'));
    });

    $('#modalHapusAlat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var modal = $(this);
        modal.find('#hapus_nama_text').text(nama);
        modal.find('#btnKonfirmasiHapus').attr('href', 'hapus_alat.php?id=' + id + '&redirect=index.php');
    });

    $('#modalDetailAlat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#detail_id').text(button.data('id'));
        modal.find('#detail_nama_alat').text(button.data('nama'));
        modal.find('#detail_tahun').text(button.data('tahun'));
        modal.find('#detail_merek').text(button.data('merek'));
        modal.find('#detail_lokasi').text(button.data('lokasi'));
    });
});
</script>

<?php include_once("includes/footer.php"); ?>
