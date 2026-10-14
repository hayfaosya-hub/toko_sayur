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

// Ambil semua data user
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM user ORDER BY id_user DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Data Kasir - Toko Sayur</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

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

        .badge-admin {
            background-color: #334155;
        }

        .badge-kasir {
            background-color: #64748b;
        }

    </style>

</head>

<body>


<!-- Navbar -->
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


        <!-- Sidebar -->
        <div class="col-md-2 sidebar">

            <a href="dashboard.php">
                🏠 Dashboard
            </a>

            <a href="produk.php">
                🥬 Data Produk
            </a>

            <a href="user.php"
               class="active">
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


            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="fw-bold mb-1">
                        Data Kasir
                    </h3>

                    <p class="text-muted mb-0">
                        Kelola akun kasir toko.
                    </p>

                </div>

                <a href="tambah_user.php"
                   class="btn btn-tambah">
                    + Tambah Kasir
                </a>

            </div>


            <!-- Pesan -->
            <?php if (isset($_GET['pesan'])): ?>

                <?php if ($_GET['pesan'] == 'tambah'): ?>

                    <div class="alert alert-success">
                        Akun berhasil ditambahkan.
                    </div>

                <?php elseif ($_GET['pesan'] == 'edit'): ?>

                    <div class="alert alert-success">
                        Akun berhasil diubah.
                    </div>

                <?php elseif ($_GET['pesan'] == 'hapus'): ?>

                    <div class="alert alert-success">
                        Akun berhasil dihapus.
                    </div>

                <?php elseif ($_GET['pesan'] == 'gagal_hapus'): ?>

                    <div class="alert alert-danger">
                        Akun gagal dihapus.
                    </div>

                <?php endif; ?>

            <?php endif; ?>


            <!-- Tabel -->
            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead>

                                <tr>

                                    <th width="70">
                                        No
                                    </th>

                                    <th>
                                        Nama Lengkap
                                    </th>

                                    <th>
                                        Username
                                    </th>

                                    <th>
                                        Password
                                    </th>

                                    <th>
                                        Role
                                    </th>

                                    <th width="180">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $no = 1;

                                if (mysqli_num_rows($query) > 0):

                                    while ($data = mysqli_fetch_assoc($query)):

                                ?>

                                    <tr>

                                        <td>
                                            <?= $no++; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $data['nama_lengkap']
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $data['username']
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $data['password']
                                            ); ?>
                                        </td>

                                        <td>

                                            <?php if ($data['role'] == 'admin'): ?>

                                                <span class="badge badge-admin">
                                                    Admin
                                                </span>

                                            <?php else: ?>

                                                <span class="badge badge-kasir">
                                                    Kasir
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <a
                                                href="edit_user.php?id=<?= $data['id_user']; ?>"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <?php
                                            // Admin tidak boleh dihapus dari halaman ini
                                            if ($data['role'] == 'kasir'):
                                            ?>

                                                <a
                                                    href="hapus_user.php?id=<?= $data['id_user']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus akun kasir ini?')">
                                                    Hapus
                                                </a>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php

                                    endwhile;

                                else:

                                ?>

                                    <tr>

                                        <td
                                            colspan="6"
                                            class="text-center py-4 text-muted">

                                            Belum ada data user.

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