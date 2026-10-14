<?php
session_start();
include '../koneksi.php';

// Cek login admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Tanggal default
$tanggal_awal = isset($_GET['tanggal_awal']) && $_GET['tanggal_awal'] != ''
    ? $_GET['tanggal_awal']
    : date('Y-m-01');

$tanggal_akhir = isset($_GET['tanggal_akhir']) && $_GET['tanggal_akhir'] != ''
    ? $_GET['tanggal_akhir']
    : date('Y-m-d');

// ===============================
// TOTAL TRANSAKSI
// ===============================
$query_total_transaksi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM penjualan
     WHERE DATE(tgl_penjualan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'"
);

$data_total_transaksi = mysqli_fetch_assoc($query_total_transaksi);
$total_transaksi = $data_total_transaksi['total'];

// ===============================
// TOTAL PENDAPATAN
// ===============================
$query_total_pendapatan = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(total_harga), 0) AS total
     FROM penjualan
     WHERE DATE(tgl_penjualan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'"
);

$data_total_pendapatan = mysqli_fetch_assoc($query_total_pendapatan);
$total_pendapatan = $data_total_pendapatan['total'];

// ===============================
// TOTAL PRODUK TERJUAL
// ===============================
$query_produk_terjual = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(dp.jumlah_produk), 0) AS total
     FROM detail_penjualan dp
     INNER JOIN penjualan p
        ON dp.id_penjualan = p.id_penjualan
     WHERE DATE(p.tgl_penjualan)
        BETWEEN '$tanggal_awal' AND '$tanggal_akhir'"
);

$data_produk_terjual = mysqli_fetch_assoc($query_produk_terjual);
$total_produk_terjual = $data_produk_terjual['total'];

// ===============================
// DATA LAPORAN PENJUALAN
// ===============================
$query_laporan = mysqli_query(
    $koneksi,
    "SELECT
        p.id_penjualan,
        p.tgl_penjualan,
        p.total_harga,
        p.nama_pelanggan_transaksi,
        pl.nama_pelanggan,
        u.nama_lengkap AS nama_kasir
     FROM penjualan p

     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan

     LEFT JOIN user u
        ON p.id_user = u.id_user

     WHERE DATE(p.tgl_penjualan)
        BETWEEN '$tanggal_awal' AND '$tanggal_akhir'

     ORDER BY p.tgl_penjualan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Penjualan - Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

    body {
        background-color: #f1f5f9;
        font-family: Arial, sans-serif;
    }

    /* NAVBAR */

    .navbar {
        background-color: #1e293b;
    }

    .navbar-brand,
    .navbar-text {
        color: white !important;
    }


    /* SIDEBAR */

    .sidebar {
        min-height: calc(100vh - 56px);
        background-color: #334155;
        padding-top: 20px;
    }

    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 12px 20px;
        margin: 4px 10px;
        border-radius: 8px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #475569;
    }


    /* CONTENT */

    .content {
        padding: 30px;
    }


    /* CARD */

    .card-custom {
        background-color: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }


    /* HEADER */

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h3 {
        color: #1e293b;
        font-weight: bold;
    }


    /* CARD STATISTIK */

    .card-stat {
        background-color: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }


    /* FILTER */

    .filter-card {
        background-color: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }


    /* TABLE */

    .table {
        vertical-align: middle;
    }

    .table thead th {
        background-color: #334155;
        color: white;
        white-space: nowrap;
    }

    .table tbody td {
        white-space: nowrap;
    }


    /* BUTTON */

    .btn-detail {
        background-color: #334155;
        color: white;
    }

    .btn-detail:hover {
        background-color: #1e293b;
        color: white;
    }


    /* EMPTY */

    .empty-data {
        padding: 50px;
        text-align: center;
        color: #64748b;
    }


    /* RESPONSIVE */

    @media (max-width: 768px) {

        .sidebar {
            min-height: auto;
        }

        .content {
            padding: 20px;
        }

    }


    /* PRINT */

    @media print {

        .sidebar,
        .navbar,
        .filter-card,
        .btn,
        .no-print {
            display: none !important;
        }

        .content {
            padding: 0;
        }

        body {
            background: white;
        }

        .card {
            box-shadow: none !important;
        }

        .table th {
            background-color: #ddd !important;
            color: black !important;
        }

    }

</style>
</head>

<body>

<body>

<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-expand-lg">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold"
           href="dashboard.php">

            Toko Sayur

        </a>

        <span class="navbar-text">

            Admin:
            <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>

        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">

        <!-- =================================================
             SIDEBAR
        ================================================= -->

        <div class="col-md-2 sidebar">

            <a href="dashboard.php">

                🏠 Dashboard

            </a>


            <a href="produk.php">

                🥬 Data Produk

            </a>


            <a href="user.php">

                👤 Data Kasir

            </a>


            <a href="pelanggan.php">

                👥 Data Pelanggan

            </a>


            <a href="penjualan.php">

                🧾 Data Penjualan

            </a>


            <a href="laporan.php"
            class="active">

                📊 Laporan

            </a>


            <a href="../logout.php"
               onclick="return confirm('Apakah Anda yakin ingin logout?')">

                🚪 Logout

            </a>

        </div>


        <!-- CONTENT -->
        <div class="col-md-9 col-lg-10 content">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2 class="judul mb-1">
                        Laporan Penjualan
                    </h2>

                    <p class="text-muted mb-0">
                        Ringkasan penjualan toko sayur
                    </p>
                </div>

                <button
                    onclick="window.print()"
                    class="btn btn-dark"
                >
                    🖨 Cetak Laporan
                </button>

            </div>


            <!-- FILTER -->
            <div class="card filter-card shadow-sm mb-4">

                <div class="card-body">

                    <form method="GET">

                        <div class="row align-items-end">

                            <div class="col-md-4 mb-3 mb-md-0">

                                <label class="form-label fw-semibold">
                                    Tanggal Awal
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_awal"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggal_awal); ?>"
                                    required
                                >

                            </div>


                            <div class="col-md-4 mb-3 mb-md-0">

                                <label class="form-label fw-semibold">
                                    Tanggal Akhir
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_akhir"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggal_akhir); ?>"
                                    required
                                >

                            </div>


                            <div class="col-md-4">

                                <button
                                    type="submit"
                                    class="btn btn-dark"
                                >
                                    🔍 Tampilkan Laporan
                                </button>

                                <a
                                    href="laporan.php"
                                    class="btn btn-secondary"
                                >
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- PERIODE -->
            <div class="alert alert-secondary">

                <strong>Periode laporan:</strong>

                <?= date('d-m-Y', strtotime($tanggal_awal)); ?>

                s/d

                <?= date('d-m-Y', strtotime($tanggal_akhir)); ?>

            </div>


            <!-- STATISTIK -->
            <div class="row mb-4">

                <!-- TOTAL TRANSAKSI -->
                <div class="col-md-4 mb-3">

                    <div class="card card-stat shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-1">
                                Total Transaksi
                            </p>

                            <h3 class="fw-bold">
                                <?= number_format($total_transaksi, 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <!-- TOTAL PENDAPATAN -->
                <div class="col-md-4 mb-3">

                    <div class="card card-stat shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-1">
                                Total Pendapatan
                            </p>

                            <h3 class="fw-bold">
                                Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <!-- PRODUK TERJUAL -->
                <div class="col-md-4 mb-3">

                    <div class="card card-stat shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-1">
                                Produk Terjual
                            </p>

                            <h3 class="fw-bold">
                                <?= number_format($total_produk_terjual, 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TABEL LAPORAN -->
            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="fw-bold mb-0">
                            Rincian Penjualan
                        </h5>

                        <span class="text-muted">
                            <?= date('d/m/Y', strtotime($tanggal_awal)); ?>
                            -
                            <?= date('d/m/Y', strtotime($tanggal_akhir)); ?>
                        </span>

                    </div>


                    <div class="table-responsive">

                        <table class="table table-bordered table-hover">

                            <thead>

                                <tr>
                                    <th width="60">No</th>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Kasir</th>
                                    <th>Total</th>
                                    <th class="no-print">Detail</th>
                                </tr>

                            </thead>

                            <tbody>

                            <?php

                            $no = 1;
                            $grand_total = 0;

                            if (mysqli_num_rows($query_laporan) > 0):

                                while ($data = mysqli_fetch_assoc($query_laporan)):

                                    $grand_total += $data['total_harga'];

                                    // Nama pelanggan
                                    if (
                                        !empty($data['nama_pelanggan_transaksi'])
                                    ) {
                                        $nama_pelanggan =
                                            $data['nama_pelanggan_transaksi'];
                                    } elseif (
                                        !empty($data['nama_pelanggan'])
                                    ) {
                                        $nama_pelanggan =
                                            $data['nama_pelanggan'];
                                    } else {
                                        $nama_pelanggan = 'Umum';
                                    }

                            ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        #<?= $data['id_penjualan']; ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'd-m-Y H:i',
                                            strtotime($data['tgl_penjualan'])
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($nama_pelanggan); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $data['nama_kasir'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td class="fw-semibold">
                                        Rp <?= number_format(
                                            $data['total_harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </td>

                                    <td class="no-print">

                                        <a
                                            href="detail_penjualan.php?id=<?= $data['id_penjualan']; ?>"
                                            class="btn btn-sm btn-outline-dark"
                                        >
                                            Detail
                                        </a>

                                    </td>

                                </tr>

                            <?php

                                endwhile;

                            else:

                            ?>

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-4"
                                    >
                                        Tidak ada data penjualan pada periode
                                        yang dipilih.
                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>


                            <?php if ($total_transaksi > 0): ?>

                            <tfoot>

                                <tr class="fw-bold">

                                    <td
                                        colspan="5"
                                        class="text-end"
                                    >
                                        TOTAL PENDAPATAN
                                    </td>

                                    <td>
                                        Rp <?= number_format(
                                            $grand_total,
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </td>

                                    <td class="no-print"></td>

                                </tr>

                            </tfoot>

                            <?php endif; ?>

                        </table>

                    </div>

                </div>

            </div>


            <!-- FOOTER CETAK -->
            <div class="d-none d-print-block mt-4">

                <p>
                    Dicetak pada:
                    <?= date('d-m-Y H:i'); ?>
                </p>

                <p>
                    <strong>
                        Toko Sayur
                    </strong>
                </p>

            </div>

        </div>

    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>