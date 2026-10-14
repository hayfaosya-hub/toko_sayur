<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

// Cek role admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil semua data produk
$query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id_produk DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Produk - Toko Sayur</title>

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

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .table {
            vertical-align: middle;
        }

        .table thead {
            background-color: #334155;
            color: white;
        }

        .btn-tambah {
            background-color: #334155;
            color: white;
        }

        .btn-tambah:hover {
            background-color: #1e293b;
            color: white;
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

            <a href="dashboard.php">
                🏠 Dashboard
            </a>

            <a href="produk.php" class="active">
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

            <a href="../logout.php"
               onclick="return confirm('Apakah Anda yakin ingin logout?')">
                🚪 Logout
            </a>

        </div>


        <!-- Content -->
        <div class="col-md-10 content">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h3 class="fw-bold mb-1">Data Produk</h3>
                    <p class="text-muted mb-0">
                        Kelola data sayuran dan produk toko.
                    </p>
                </div>

                <a href="tambah_produk.php" class="btn btn-tambah">
                    + Tambah Produk
                </a>

            </div>


            <!-- Pesan -->
            <?php if (isset($_GET['pesan'])): ?>

                <?php if ($_GET['pesan'] == 'tambah'): ?>

                    <div class="alert alert-success">
                        Produk berhasil ditambahkan.
                    </div>

                <?php elseif ($_GET['pesan'] == 'edit'): ?>

                    <div class="alert alert-success">
                        Produk berhasil diubah.
                    </div>

                <?php elseif ($_GET['pesan'] == 'hapus'): ?>

                    <div class="alert alert-success">
                        Produk berhasil dihapus.
                    </div>

                <?php endif; ?>

            <?php endif; ?>


            <!-- Tabel Produk -->
            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead>
                                <tr>
                                    <th width="70">No</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($query) > 0):

                                    while ($data = mysqli_fetch_assoc($query)):
                                ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_produk']); ?>
                                        </td>

                                        <td>
                                            Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['stok']); ?>
                                        </td>

                                        <td>

                                            <a href="edit_produk.php?id=<?= $data['id_produk']; ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="hapus_produk.php?id=<?= $data['id_produk']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                Hapus
                                            </a>

                                        </td>

                                    </tr>

                                <?php
                                    endwhile;

                                else:
                                ?>

                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada data produk.
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>