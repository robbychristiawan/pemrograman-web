<?php
require_once("config.php");
cek_login();

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Data Alat Medis</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-kop h3 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        .header-kop p {
            margin: 0;
            font-size: 12px;
            color: #555;
        }
        .meta-info {
            margin-bottom: 15px;
        }
        .meta-info table td {
            padding: 3px 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #444;
            padding: 8px 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 40px;
            float: right;
            text-align: center;
            width: 250px;
        }
        .signature-space {
            height: 70px;
        }
        .no-print {
            margin-bottom: 15px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print();" style="padding: 8px 16px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak Sekarang (Print / PDF)
        </button>
        <button onclick="window.close();" style="padding: 8px 16px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="header-kop">
        <h2>RUMAH SAKIT UMUM SIMRS</h2>
        <h3>INSTALASI PEMELIHARAAN SARANA RUMAH SAKIT (IPSRS)</h3>
        <p>Jl. Kesehatan No. 123, Telp: (021) 555-0123, Email: ipsrs@simrs.local</p>
    </div>

    <div class="meta-info">
        <h3 style="text-align: center; margin-bottom: 10px;">LAPORAN INVENTARIS ALAT ELEKTROMEDIS</h3>
        <table>
            <tr>
                <td><strong>Filter Lokasi/Ruangan</strong></td>
                <td>: <?= !empty($filter_lokasi) ? htmlspecialchars($filter_lokasi) : 'Semua Ruangan'; ?></td>
            </tr>
            <tr>
                <td><strong>Filter Tahun</strong></td>
                <td>: <?= !empty($filter_tahun) ? 'Tahun ' . htmlspecialchars($filter_tahun) : 'Semua Tahun'; ?></td>
            </tr>
            <tr>
                <td><strong>Waktu Cetak</strong></td>
                <td>: <?= date('d F Y, H:i'); ?> WIB</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: <?= htmlspecialchars($_SESSION['nama_lengkap']); ?> (<?= htmlspecialchars($_SESSION['username']); ?>)</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>Nama Alat Elektromedis</th>
                <th style="width: 80px; text-align: center;">Tahun</th>
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
                    <td style="text-align: center;"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_alat']); ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($row['tahun']); ?></td>
                    <td><?= htmlspecialchars($row['merek']); ?></td>
                    <td><?= htmlspecialchars($row['lokasi']); ?></td>
                </tr>
            <?php 
                endwhile; 
            else:
            ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data yang ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        <strong>Total Alat: <?= $total_data; ?> Unit</strong>
    </div>

    <div class="signature-section">
        <p>Mengetahui,<br><strong>Kepala Instalasi IPSRS</strong></p>
        <div class="signature-space"></div>
        <p><u>( <?= htmlspecialchars($_SESSION['nama_lengkap']); ?> )</u><br>NIP. 19850101 201012 1 001</p>
    </div>

    <script>
        // Otomatis muncul dialog print saat halaman dibuka
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
