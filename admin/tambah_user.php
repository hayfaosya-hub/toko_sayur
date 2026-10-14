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

// Proses tambah user
if (isset($_POST['simpan'])) {

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
    if ($nama_lengkap == '' || $username == '' || $password == '') {

        $error = "Semua data wajib diisi.";

    } else {

        // Cek username apakah sudah digunakan
        $cek_username = mysqli_query(
            $koneksi,
            "SELECT * FROM user WHERE username='$username'"
        );

        if (mysqli_num_rows($cek_username) > 0) {

            $error = "Username sudah digunakan. Silakan gunakan username lain.";

        } else {

            // Role otomatis kasir
            $role = 'kasir';

            $query = mysqli_query(
                $koneksi,
                "INSERT INTO user
                (nama_lengkap, username, password, role)
                VALUES
                ('$nama_lengkap', '$username', '$password', '$role')"
            );

            if ($query) {

                header("Location: user.php?pesan=tambah");
                exit;

            } else {

                $error = "Akun gagal ditambahkan: " . mysqli_error($koneksi);

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

    <title>Tambah Kasir - Toko Sayur</title>

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
                    Tambah Kasir
                </h3>

                <p class="text-muted mb-0">
                    Buat akun baru untuk kasir.
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
                                placeholder="Contoh: Hayfa Osya"
                                value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>"
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
                                placeholder="Contoh: kasir_osya"
                                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                required
                            >

                        </div>


                        <!-- Password -->
                        <div class="mb-4">

                            <label
                                for="password"
                                class="form-label fw-semibold">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                required
                            >

                        </div>


                        <!-- Role -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Role
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Kasir"
                                readonly
                            >

                            <small class="text-muted">
                                Akun yang dibuat melalui halaman ini otomatis memiliki role Kasir.
                            </small>

                        </div>


                        <!-- Tombol -->
                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                name="simpan"
                                class="btn btn-simpan">
                                Simpan Kasir
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