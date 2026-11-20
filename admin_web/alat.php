<?php
require_once("config.php");
cek_login();

$page_title = "Data Produk / Alat Medis";
$current_page = "alat";

$pesan = $_GET['pesan'] ?? '';
$error = '';

// Handle POST Aksi (Tambah & Edit) langsung via Pop-up Modal
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
                header("Location: alat.php?pesan=tambah_sukses");
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
                header("Location: alat.php?pesan=edit_sukses");
                exit;
            } else {
                $error = "Gagal memperbarui data: " . mysqli_error($mysqli);
            }
        }
    }
}

$cari = isset($_GET['cari']) ? trim(mysqli_real_escape_string($mysqli, $_GET['cari'])) : '';

if (!empty($cari)) {
    $query = "SELECT * FROM alat WHERE nama_alat LIKE '%$cari%' OR merek LIKE '%$cari%' OR lokasi LIKE '%$cari%' OR tahun LIKE '%$cari%' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM alat ORDER BY id DESC";
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
                <div class="page_title d-flex justify-content-between align-items-center">
                    <h2>Manajemen Data Produk / Alat Elektromedis</h2>
                    <!-- Tombol memunculkan Modal Pop-up Tambah -->
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambahAlat">
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

        <!-- Table Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head d-flex justify-content-between align-items-center flex-wrap">
                        <h2><i class="fa fa-list mr-2"></i> Daftar Alat Medis (Total: <?= mysqli_num_rows($result); ?>)</h2>
                        <form action="alat.php" method="GET" class="form-inline mt-2 mt-md-0">
                            <div class="input-group">
                                <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama, merek, lokasi..." value="<?= htmlspecialchars($cari); ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                    <?php if (!empty($cari)): ?>
                                        <a href="alat.php" class="btn btn-sm btn-secondary" title="Reset"><i class="fa fa-refresh"></i></a>
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
                                        <th>Nama Alat</th>
                                        <th>Tahun Pengadaan</th>
                                        <th>Merek</th>
                                        <th>Lokasi Ruangan</th>
                                        <th class="text-center" style="width: 200px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    if (mysqli_num_rows($result) > 0):
                                        while ($row = mysqli_fetch_array($result)): 
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($row['nama_alat']); ?></strong></td>
                                            <td><span class="badge badge-secondary"><?= htmlspecialchars($row['tahun']); ?></span></td>
                                            <td><?= htmlspecialchars($row['merek']); ?></td>
                                            <td><span class="badge badge-info p-2"><?= htmlspecialchars($row['lokasi']); ?></span></td>
                                            <td class="text-center">
                                                <!-- Tombol Detail Modal -->
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
                                                <!-- Tombol Edit Modal -->
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
                                                <!-- Tombol Hapus Modal -->
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
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fa fa-info-circle fa-2x mb-2 d-block"></i>
                                                Tidak ditemukan data yang sesuai<?php if(!empty($cari)) echo ' dengan kata kunci "<b>'.htmlspecialchars($cari).'</b>"'; ?>.
                                            </td>
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
<!-- 1. MODAL POP-UP TAMBAH ALAT                                    -->
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
            <form action="alat.php" method="POST">
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
<!-- 2. MODAL POP-UP EDIT ALAT                                      -->
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
            <form action="alat.php" method="POST">
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
<!-- 3. MODAL POP-UP KONFIRMASI HAPUS ALAT                          -->
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
<!-- 4. MODAL POP-UP DETAIL ALAT                                    -->
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Populate Modal Edit
    $('#modalEditAlat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var tahun = button.data('tahun');
        var merek = button.data('merek');
        var lokasi = button.data('lokasi');

        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_nama_alat').val(nama);
        modal.find('#edit_tahun').val(tahun);
        modal.find('#edit_merek').val(merek);
        modal.find('#edit_lokasi').val(lokasi);
    });

    // Populate Modal Hapus
    $('#modalHapusAlat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');

        var modal = $(this);
        modal.find('#hapus_nama_text').text(nama);
        modal.find('#btnKonfirmasiHapus').attr('href', 'hapus_alat.php?id=' + id + '&redirect=' + encodeURIComponent(window.location.pathname));
    });

    // Populate Modal Detail
    $('#modalDetailAlat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#detail_id').text(button.data('id'));
        modal.find('#detail_nama_alat').text(button.data('nama'));
        modal.find('#detail_tahun').text(button.data('tahun'));
        modal.find('#detail_merek').text(button.data('merek'));
        modal.find('#detail_lokasi').text(button.data('lokasi'));
    });

    // Buka pop-up otomatis jika parameter URL ?modal=tambah
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('modal') === 'tambah') {
        $('#modalTambahAlat').modal('show');
    }
});
</script>

<?php include_once("includes/footer.php"); ?>
