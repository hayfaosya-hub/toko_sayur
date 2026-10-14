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

// Proses tambah produk
if (isset($_POST['simpan'])) {

    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga       = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok        = mysqli_real_escape_string($koneksi, $_POST['stok']);

    // Validasi sederhana
    if ($nama_produk == '' || $harga == '' || $stok == '') {
        $error = "Semua data produk wajib diisi.";
    } elseif ($harga < 0 || $stok < 0) {
        $error = "Harga dan stok tidak boleh kurang dari 0.";
    } else {

        $query = mysqli_query(
            $koneksi,
            "INSERT INTO produk (nama_produk, harga, stok)
             VALUES ('$nama_produk', '$harga', '$stok')"
        );

        if ($query) {
            header("Location: produk.php?pesan=tambah");
            exit;
        } else {
            $error = "Produk gagal ditambahkan: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk - Toko Sayur</title>

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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .btn-simpan {
            background-color: #334155;
            color: white;
        }

        .btn-simpan:hover {
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

            <div class="mb-4">
                <h3 class="fw-bold mb-1">Tambah Produk</h3>
                <p class="text-muted mb-0">
                    Tambahkan produk atau sayuran baru ke dalam data toko.
                </p>
            </div>


            <!-- Pesan Error -->
            <?php if (isset($error)): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <div class="card">

                <div class="card-body p-4">

                    <form method="POST">

                        <!-- Nama Produk -->
                        <div class="mb-3">

                            <label for="nama_produk" class="form-label fw-semibold">
                                Nama Produk
                            </label>

                            <input
                                type="text"
                                name="nama_produk"
                                id="nama_produk"
                                class="form-control"
                                placeholder="Contoh: Bayam"
                                value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : ''; ?>"
                                required
                            >

                        </div>


                        <!-- Harga -->
                        <div class="mb-3">

                            <label for="harga" class="form-label fw-semibold">
                                Harga
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="harga"
                                    id="harga"
                                    class="form-control"
                                    placeholder="Contoh: 5000"
                                    min="0"
                                    value="<?= isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>"
                                    required
                                >

                            </div>

                            <small class="text-muted">
                                Masukkan harga tanpa titik atau koma.
                            </small>

                        </div>


                        <!-- Stok -->
                        <div class="mb-4">

                            <label for="stok" class="form-label fw-semibold">
                                Stok
                            </label>

                            <input
                                type="number"
                                name="stok"
                                id="stok"
                                class="form-control"
                                placeholder="Contoh: 20"
                                min="0"
                                value="<?= isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : ''; ?>"
                                required
                            >

                        </div>


                        <!-- Tombol -->
                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                name="simpan"
                                class="btn btn-simpan"
                            >
                                Simpan Produk
                            </button>

                            <a href="produk.php" class="btn btn-secondary">
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>