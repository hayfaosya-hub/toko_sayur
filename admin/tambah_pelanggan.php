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

// Proses tambah pelanggan
if (isset($_POST['simpan'])) {

    $nama_pelanggan = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_pelanggan']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    $no_telp = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_telp']
    );

    // Validasi
    if ($nama_pelanggan == '') {

        $error = "Nama pelanggan wajib diisi.";

    } else {

        $query = mysqli_query(
            $koneksi,
            "INSERT INTO pelanggan
            (nama_pelanggan, alamat, no_telp)
            VALUES
            ('$nama_pelanggan', '$alamat', '$no_telp')"
        );

        if ($query) {

            header("Location: pelanggan.php?pesan=tambah");
            exit;

        } else {

            $error = "Pelanggan gagal ditambahkan: " . mysqli_error($koneksi);

        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Pelanggan - Toko Sayur</title>

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

            <a href="produk.php">
                🥬 Data Produk
            </a>

            <a href="user.php">
                👤 Data Kasir
            </a>

            <a href="pelanggan.php" class="active">
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
                <h3 class="fw-bold mb-1">
                    Tambah Pelanggan
                </h3>

                <p class="text-muted mb-0">
                    Tambahkan data pelanggan baru.
                </p>
            </div>

            <?php if (isset($error)): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <div class="card">

                <div class="card-body p-4">

                    <form method="POST">

                        <div class="mb-3">

                            <label for="nama_pelanggan"
                                   class="form-label fw-semibold">
                                Nama Pelanggan
                            </label>

                            <input type="text"
                                   name="nama_pelanggan"
                                   id="nama_pelanggan"
                                   class="form-control"
                                   placeholder="Contoh: Budi"
                                   value="<?= isset($_POST['nama_pelanggan']) ? htmlspecialchars($_POST['nama_pelanggan']) : ''; ?>"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label for="alamat"
                                   class="form-label fw-semibold">
                                Alamat
                            </label>

                            <textarea name="alamat"
                                      id="alamat"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Masukkan alamat pelanggan"><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>

                        </div>

                        <div class="mb-4">

                            <label for="no_telp"
                                   class="form-label fw-semibold">
                                No. Telepon
                            </label>

                            <input type="text"
                                   name="no_telp"
                                   id="no_telp"
                                   class="form-control"
                                   placeholder="Contoh: 081234567890"
                                   value="<?= isset($_POST['no_telp']) ? htmlspecialchars($_POST['no_telp']) : ''; ?>">

                        </div>

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-simpan">
                                Simpan Pelanggan
                            </button>

                            <a href="pelanggan.php"
                               class="btn btn-secondary">
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