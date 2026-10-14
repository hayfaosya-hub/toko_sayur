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

// Cek ID user
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: user.php");
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data user
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM user WHERE id_user='$id_user'"
);

$data = mysqli_fetch_assoc($query);

// Jika user tidak ditemukan
if (!$data) {
    header("Location: user.php");
    exit;
}

// Proses update
if (isset($_POST['update'])) {

    $nama_lengkap = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_lengkap']
    );

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = mysqli_real_escape_string(
        $koneksi,
        $_POST['password']
    );

    // Validasi
    if ($nama_lengkap == '' || $username == '') {

        $error = "Nama lengkap dan username wajib diisi.";

    } else {

        // Cek username milik user lain
        $cek_username = mysqli_query(
            $koneksi,
            "SELECT * FROM user
             WHERE username='$username'
             AND id_user != '$id_user'"
        );

        if (mysqli_num_rows($cek_username) > 0) {

            $error = "Username sudah digunakan oleh user lain.";

        } else {

            // Jika password kosong, password lama tetap digunakan
            if ($password == '') {

                $update = mysqli_query(
                    $koneksi,
                    "UPDATE user SET
                        nama_lengkap='$nama_lengkap',
                        username='$username'
                     WHERE id_user='$id_user'"
                );

            } else {

                $update = mysqli_query(
                    $koneksi,
                    "UPDATE user SET
                        nama_lengkap='$nama_lengkap',
                        username='$username',
                        password='$password'
                     WHERE id_user='$id_user'"
                );
            }

            if ($update) {

                header("Location: user.php?pesan=edit");
                exit;

            } else {

                $error = "Data user gagal diubah: " . mysqli_error($koneksi);

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit User - Toko Sayur</title>

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

            <div class="mb-4">

                <h3 class="fw-bold mb-1">
                    Edit User
                </h3>

                <p class="text-muted mb-0">
                    Ubah data akun pengguna.
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


                        <!-- Nama Lengkap -->
                        <div class="mb-3">

                            <label
                                for="nama_lengkap"
                                class="form-label fw-semibold">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                id="nama_lengkap"
                                class="form-control"
                                value="<?= htmlspecialchars($data['nama_lengkap']); ?>"
                                required
                            >

                        </div>


                        <!-- Username -->
                        <div class="mb-3">

                            <label
                                for="username"
                                class="form-label fw-semibold">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                id="username"
                                class="form-control"
                                value="<?= htmlspecialchars($data['username']); ?>"
                                required
                            >

                        </div>


                        <!-- Password -->
                        <div class="mb-4">

                            <label
                                for="password"
                                class="form-label fw-semibold">
                                Password Baru
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Kosongkan jika tidak ingin mengubah password"
                            >

                            <small class="text-muted">
                                Kosongkan jika password tidak ingin diubah.
                            </small>

                        </div>


                        <!-- Role -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Role
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars(ucfirst($data['role'])); ?>"
                                readonly
                            >

                        </div>


                        <!-- Tombol -->
                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                name="update"
                                class="btn btn-simpan">
                                Simpan Perubahan
                            </button>

                            <a
                                href="user.php"
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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>