<?php
session_start();
include '../koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

// Cek apakah role adalah kasir
if ($_SESSION['role'] != 'kasir') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ==========================================
// JUMLAH PRODUK
// ==========================================
$query_produk = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM produk"
);

$data_produk = mysqli_fetch_assoc($query_produk);
$total_produk = $data_produk['total'];


// ==========================================
// JUMLAH PELANGGAN
// ==========================================
$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT COUNT(DISTINCT nama_pelanggan_transaksi) AS total
     FROM penjualan
     WHERE nama_pelanggan_transaksi IS NOT NULL
     AND nama_pelanggan_transaksi != ''"
);

$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$total_pelanggan = $data_pelanggan['total'];


// ==========================================
// JUMLAH TRANSAKSI HARI INI
// ==========================================
$query_transaksi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM penjualan
     WHERE DATE(tgl_penjualan) = CURDATE()
     AND id_user = '$id_user'"
);

$data_transaksi = mysqli_fetch_assoc($query_transaksi);
$total_transaksi = $data_transaksi['total'];


// ==========================================
// TOTAL PENJUALAN HARI INI
// ==========================================
$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(total_harga), 0) AS total
     FROM penjualan
     WHERE DATE(tgl_penjualan) = CURDATE()
     AND id_user = '$id_user'"
);

$data_penjualan = mysqli_fetch_assoc($query_penjualan);
$total_penjualan = $data_penjualan['total'];


// ==========================================
// TRANSAKSI TERAKHIR
// ==========================================
$query_terakhir = mysqli_query(
    $koneksi,
    "SELECT 
        p.id_penjualan,
        p.tgl_penjualan,
        p.total_harga,
        p.nama_pelanggan_transaksi,
        pl.nama_pelanggan
     FROM penjualan p
     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan
     WHERE p.id_user = '$id_user'
     ORDER BY p.id_penjualan DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard Kasir - Toko Sayur</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        /* NAVBAR */
        .navbar {
            background-color: #1e293b;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }

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

        /* WELCOME */
        .welcome {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        /* CARD */
        .stat-card {
            background-color: white;
            border: none;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            height: 100%;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background-color: #e2e8f0;
            color: #334155;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-title {
            color: #64748b;
            font-size: 14px;
            margin-top: 15px;
        }

        .stat-number {
            color: #1e293b;
            font-size: 28px;
            font-weight: bold;
        }

        /* TRANSAKSI */
        .transaction-card {
            background-color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .transaction-card .card-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 20px;
            font-weight: bold;
        }

        /* BUTTON */
        .btn-transaksi {
            background-color: #334155;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-transaksi:hover {
            background-color: #1e293b;
            color: white;
        }

        .btn-riwayat {
            background-color: #e2e8f0;
            color: #334155;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-riwayat:hover {
            background-color: #cbd5e1;
            color: #1e293b;
        }

        .table {
            vertical-align: middle;
        }

        @media (max-width: 768px) {

            .sidebar {
                min-height: auto;
            }

            .content {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-expand-lg">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
            🥬 Toko Sayur
        </a>

        <span class="navbar-text">
            Kasir: <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">

        <!-- ==========================================
             SIDEBAR
        ========================================== -->

        <div class="col-md-2 sidebar">

            <a href="dashboard.php" class="active">
                🏠 Dashboard
            </a>

            <a href="transaksi.php">
                🛒 Transaksi
            </a>

            <a href="riwayat.php">
                🧾 Riwayat Penjualan
            </a>

            <a href="../logout.php"
               onclick="return confirm('Apakah Anda yakin ingin logout?')">
                🚪 Logout
            </a>

        </div>


        <!-- ==========================================
             CONTENT
        ========================================== -->

        <div class="col-md-10 content">


            <!-- WELCOME -->

            <div class="welcome">

                <h3 class="fw-bold mb-2">
                    Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?> 👋
                </h3>

                <p class="text-muted mb-0">
                    Selamat bekerja. Silakan lakukan transaksi penjualan melalui menu transaksi.
                </p>

            </div>


            <!-- ==========================================
                 STATISTIK
            ========================================== -->

            <div class="row g-4 mb-4">


                <!-- PRODUK -->

                <div class="col-md-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">
                            🥬
                        </div>

                        <div class="stat-title">
                            Total Produk
                        </div>

                        <div class="stat-number">
                            <?= $total_produk; ?>
                        </div>

                    </div>

                </div>


                <!-- PELANGGAN -->

                <div class="col-md-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">
                            👥
                        </div>

                        <div class="stat-title">
                            Total Pelanggan
                        </div>

                        <div class="stat-number">
                            <?= $total_pelanggan; ?>
                        </div>

                    </div>

                </div>


                <!-- TRANSAKSI -->

                <div class="col-md-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">
                            🧾
                        </div>

                        <div class="stat-title">
                            Transaksi Hari Ini
                        </div>

                        <div class="stat-number">
                            <?= $total_transaksi; ?>
                        </div>

                    </div>

                </div>


                <!-- PENJUALAN -->

                <div class="col-md-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">
                            💰
                        </div>

                        <div class="stat-title">
                            Penjualan Hari Ini
                        </div>

                        <div class="stat-number"
                             style="font-size: 20px;">

                            Rp <?= number_format($total_penjualan, 0, ',', '.'); ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==========================================
                 TOMBOL TRANSAKSI
            ========================================== -->

            <div class="welcome">

                <h5 class="fw-bold mb-2">
                    Mulai Transaksi
                </h5>

                <p class="text-muted">
                    Klik tombol di bawah untuk melakukan transaksi penjualan baru.
                </p>

                <a href="transaksi.php"
                   class="btn-transaksi">

                    🛒 Mulai Transaksi

                </a>

                <a href="riwayat.php"
                   class="btn-riwayat ms-2">

                    🧾 Riwayat Penjualan

                </a>

            </div>


            <!-- ==========================================
                 TRANSAKSI TERAKHIR
            ========================================== -->

            <div class="card transaction-card">

                <div class="card-header">

                    Transaksi Terakhir

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                                <tr>

                                    <th>No</th>

                                    <th>ID Transaksi</th>

                                    <th>Tanggal</th>

                                    <th>Pelanggan</th>

                                    <th>Total</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php

                                $no = 1;

                                if (mysqli_num_rows($query_terakhir) > 0):

                                    while ($data = mysqli_fetch_assoc($query_terakhir)):

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

                                            <?php

                                            if (!empty($data['nama_pelanggan_transaksi'])) {

                                                echo htmlspecialchars(
                                                    $data['nama_pelanggan_transaksi']
                                                );

                                            } elseif (!empty($data['nama_pelanggan'])) {

                                                echo htmlspecialchars(
                                                    $data['nama_pelanggan']
                                                );

                                            } else {

                                                echo 'Umum';

                                            }

                                            ?>

                                        </td>

                                        <td>

                                            <strong>
                                                Rp <?= number_format(
                                                    $data['total_harga'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>
                                            </strong>

                                        </td>

                                    </tr>

                                <?php

                                    endwhile;

                                else:

                                ?>

                                    <tr>

                                        <td colspan="5"
                                            class="text-center text-muted py-4">

                                            Belum ada transaksi.

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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>