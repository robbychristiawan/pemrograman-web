<?php
require_once("config.php");
cek_login();

$page_title = "Laporan & Rekap Data";
$current_page = "laporan";

// Ambil daftar filter unik
$lokasi_list_res = mysqli_query($mysqli, "SELECT DISTINCT lokasi FROM alat ORDER BY lokasi ASC");
$tahun_list_res = mysqli_query($mysqli, "SELECT DISTINCT tahun FROM alat ORDER BY tahun DESC");

$filter_lokasi = trim(mysqli_real_escape_string($mysqli, $_GET['lokasi'] ?? ''));
$filter_tahun  = trim(mysqli_real_escape_string($mysqli, $_GET['tahun'] ?? ''));

$where_clauses = [];
if (!empty($filter_lokasi)) {
    $where_clauses[] = "lokasi = '$filter_lokasi'";
}
if (!empty($filter_tahun)) {
    $where_clauses[] = "tahun = '$filter_tahun'";
}

$query = "SELECT * FROM alat";
if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}
$query .= " ORDER BY lokasi ASC, nama_alat ASC";

$result = mysqli_query($mysqli, $query);
$total_data = mysqli_num_rows($result);

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
                    <h2>Laporan & Rekapitulasi Data Alat Medis</h2>
                    <div>
                        <a href="cetak_laporan.php?lokasi=<?= urlencode($filter_lokasi); ?>&tahun=<?= urlencode($filter_tahun); ?>" target="_blank" class="btn btn-danger font-weight-bold">
                            <i class="fa fa-print mr-1"></i> Cetak / Simpan PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head">
                        <h2><i class="fa fa-filter mr-2"></i> Filter Data Laporan</h2>
                    </div>
                    <div class="padding_infor_info">
                        <form action="laporan.php" method="GET" class="form-row align-items-end">
                            <div class="form-group col-md-4">
                                <label class="font-weight-bold">Ruangan / Lokasi</label>
                                <select name="lokasi" class="form-control">
                                    <option value="">-- Semua Ruangan / Lokasi --</option>
                                    <?php while ($loc = mysqli_fetch_assoc($lokasi_list_res)): ?>
                                        <option value="<?= htmlspecialchars($loc['lokasi']); ?>" <?= ($filter_lokasi === $loc['lokasi']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($loc['lokasi']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-bold">Tahun Pengadaan</label>
                                <select name="tahun" class="form-control">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php while ($th = mysqli_fetch_assoc($tahun_list_res)): ?>
                                        <option value="<?= htmlspecialchars($th['tahun']); ?>" <?= ($filter_tahun === $th['tahun']) ? 'selected' : ''; ?>>
                                            Tahun <?= htmlspecialchars($th['tahun']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <button type="submit" class="btn btn-primary px-4"><i class="fa fa-search mr-1"></i> Terapkan Filter</button>
                                <?php if (!empty($filter_lokasi) || !empty($filter_tahun)): ?>
                                    <a href="laporan.php" class="btn btn-secondary ml-1"><i class="fa fa-refresh mr-1"></i> Reset</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Table Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full_graph_head d-flex justify-content-between align-items-center">
                        <h2><i class="fa fa-table mr-2"></i> Hasil Laporan (<?= $total_data; ?> Data Ditemukan)</h2>
                        <span class="text-muted">
                            Status Filter: 
                            <b><?= !empty($filter_lokasi) ? htmlspecialchars($filter_lokasi) : 'Semua Lokasi'; ?></b>, 
                            <b><?= !empty($filter_tahun) ? 'Tahun ' . htmlspecialchars($filter_tahun) : 'Semua Tahun'; ?></b>
                        </span>
                    </div>

                    <div class="table_section padding_infor_info">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Alat</th>
                                        <th>Tahun</th>
                                        <th>Merek</th>
                                        <th>Lokasi / Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    if ($total_data > 0):
                                        while ($row = mysqli_fetch_assoc($result)): 
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><strong><?= htmlspecialchars($row['nama_alat']); ?></strong></td>
                                            <td><?= htmlspecialchars($row['tahun']); ?></td>
                                            <td><?= htmlspecialchars($row['merek']); ?></td>
                                            <td><span class="badge badge-info p-1"><?= htmlspecialchars($row['lokasi']); ?></span></td>
                                        </tr>
                                    <?php 
                                        endwhile; 
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data alat yang sesuai dengan filter yang dipilih.</td>
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

<?php include_once("includes/footer.php"); ?>
