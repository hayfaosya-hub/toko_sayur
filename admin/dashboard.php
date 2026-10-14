<?php
session_start();
include '../koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

// Cek apakah role adalah admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data untuk dashboard
$query_produk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$data_produk = mysqli_fetch_assoc($query_produk);
$total_produk = $data_produk['total'];

$query_kasir = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='kasir'");
$data_kasir = mysqli_fetch_assoc($query_kasir);
$total_kasir = $data_kasir['total'];

$query_pelanggan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pelanggan");
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$total_pelanggan = $data_pelanggan['total'];

$query_penjualan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM penjualan");
$data_penjualan = mysqli_fetch_assoc($query_penjualan);
$total_penjualan = $data_penjualan['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Toko Sayur</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #1e293b;
        }

        .navbar-brand,
        .navbar-text {
            color: white !important;
        }

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

        .content {
            padding: 30px;
        }

        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .stat-title {
            color: #64748b;
            font-size: 14px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
        }

        .welcome {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            Toko Sayur
        </a>

        <span class="navbar-text">
            Admin: <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
        </span>
    </div>
</nav>


<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar">

            <a href="dashboard.php" class="active">
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

            <a href="laporan.php">
                📊 Laporan
            </a>

            <a href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                🚪 Logout
            </a>

        </div>


        <!-- Content -->
        <div class="col-md-10 content">

            <div class="welcome">
                <h3>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?> 👋</h3>
                <p class="mb-0 text-muted">
                    Anda login sebagai <strong>Admin</strong>.
                    Kelola data toko sayur melalui menu di sebelah kiri.
                </p>
            </div>


            <!-- Statistik -->
            <div class="row g-4">

                <!-- Produk -->
                <div class="col-md-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="stat-title">Total Produk</div>
                            <div class="stat-number">
                                <?= $total_produk; ?>
                            </div>
                            <a href="produk.php" class="btn btn-sm btn-outline-dark mt-2">
                                Kelola Produk
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Kasir -->
                <div class="col-md-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="stat-title">Total Kasir</div>
                            <div class="stat-number">
                                <?= $total_kasir; ?>
                            </div>
                            <a href="user.php" class="btn btn-sm btn-outline-dark mt-2">
                                Kelola Kasir
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Pelanggan -->
                <div class="col-md-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="stat-title">Total Pelanggan</div>
                            <div class="stat-number">
                                <?= $total_pelanggan; ?>
                            </div>
                            <a href="pelanggan.php" class="btn btn-sm btn-outline-dark mt-2">
                                Data Pelanggan
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Penjualan -->
                <div class="col-md-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="stat-title">Total Penjualan</div>
                            <div class="stat-number">
                                <?= $total_penjualan; ?>
                            </div>
                            <a href="penjualan.php" class="btn btn-sm btn-outline-dark mt-2">
                                Lihat Penjualan
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>