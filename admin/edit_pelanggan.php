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

// Cek ID pelanggan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pelanggan.php");
    exit;
}

$id_pelanggan = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

// Ambil data pelanggan
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan
     WHERE id_pelanggan='$id_pelanggan'"
);

$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    header("Location: pelanggan.php");
    exit;
}

// Proses update
if (isset($_POST['update'])) {

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

    if ($nama_pelanggan == '') {

        $error = "Nama pelanggan wajib diisi.";

    } else {

        $update = mysqli_query(
            $koneksi,
            "UPDATE pelanggan SET
                nama_pelanggan='$nama_pelanggan',
                alamat='$alamat',
                no_telp='$no_telp'
             WHERE id_pelanggan='$id_pelanggan'"
        );

        if ($update) {

            header("Location: pelanggan.php?pesan=edit");
            exit;

        } else {

            $error = "Data pelanggan gagal diubah: " . mysqli_error($koneksi);

        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Pelanggan - Toko Sayur</title>

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
                    Edit Pelanggan
                </h3>

                <p class="text-muted mb-0">
                    Ubah data pelanggan.
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
                                   value="<?= htmlspecialchars($data['nama_pelanggan']); ?>"
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
                                      rows="3"><?= htmlspecialchars($data['alamat']); ?></textarea>

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
                                   value="<?= htmlspecialchars($data['no_telp']); ?>">

                        </div>

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    name="update"
                                    class="btn btn-simpan">
                                Simpan Perubahan
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