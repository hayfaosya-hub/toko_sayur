<?php
session_start();
include '../koneksi.php';

// =====================================================
// CEK LOGIN
// =====================================================

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

// =====================================================
// CEK ROLE KASIR
// =====================================================

if ($_SESSION['role'] != 'kasir') {
    header("Location: ../login.php");
    exit;
}

// =====================================================
// BUAT SESSION KERANJANG
// =====================================================

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}


// =====================================================
// TAMBAH PRODUK KE KERANJANG
// =====================================================

if (isset($_POST['tambah_produk'])) {

    $id_produk = (int) $_POST['id_produk'];
    $jumlah = (int) $_POST['jumlah'];

    if ($jumlah < 1) {
        header("Location: transaksi.php?pesan=jumlah");
        exit;
    }

    // Ambil data produk
    $query_produk = mysqli_query(
        $koneksi,
        "SELECT * FROM produk WHERE id_produk='$id_produk'"
    );

    $produk = mysqli_fetch_assoc($query_produk);

    if (!$produk) {
        header("Location: transaksi.php?pesan=produk");
        exit;
    }

    // Cek stok
    if ($jumlah > $produk['stok']) {
        header("Location: transaksi.php?pesan=stok");
        exit;
    }

    // Jika produk sudah ada di keranjang
    if (isset($_SESSION['keranjang'][$id_produk])) {

        $jumlah_baru =
            $_SESSION['keranjang'][$id_produk]['jumlah'] + $jumlah;

        // Cek total jumlah dengan stok
        if ($jumlah_baru > $produk['stok']) {
            header("Location: transaksi.php?pesan=stok");
            exit;
        }

        $_SESSION['keranjang'][$id_produk]['jumlah'] = $jumlah_baru;

        $_SESSION['keranjang'][$id_produk]['subtotal'] =
            $jumlah_baru * $produk['harga'];

    } else {

        // Produk baru
        $_SESSION['keranjang'][$id_produk] = [
            'id_produk' => $produk['id_produk'],
            'nama_produk' => $produk['nama_produk'],
            'harga' => $produk['harga'],
            'jumlah' => $jumlah,
            'subtotal' => $jumlah * $produk['harga']
        ];
    }

    header("Location: transaksi.php?pesan=tambah");
    exit;
}


// =====================================================
// HAPUS PRODUK DARI KERANJANG
// =====================================================

if (isset($_GET['hapus'])) {

    $id_produk = (int) $_GET['hapus'];

    if (isset($_SESSION['keranjang'][$id_produk])) {
        unset($_SESSION['keranjang'][$id_produk]);
    }

    header("Location: transaksi.php?pesan=hapus");
    exit;
}


// =====================================================
// KOSONGKAN KERANJANG
// =====================================================

if (isset($_GET['kosongkan'])) {

    $_SESSION['keranjang'] = [];

    header("Location: transaksi.php?pesan=kosong");
    exit;
}


// =====================================================
// AMBIL DATA PRODUK
// =====================================================

$query_produk = mysqli_query(
    $koneksi,
    "SELECT * FROM produk
     WHERE stok > 0
     ORDER BY nama_produk ASC"
);


// =====================================================
// HITUNG TOTAL KERANJANG
// =====================================================

$total = 0;

foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['subtotal'];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Transaksi - Toko Sayur</title>

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

        /* CARD */

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
            padding: 18px 20px;
        }

        /* BUTTON */

        .btn-primary-custom {
            background-color: #334155;
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #1e293b;
            color: white;
        }

        /* TOTAL */

        .total-box {
            background-color: #f8fafc;
            border-radius: 10px;
            padding: 20px;
        }

        .total-harga {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
        }

        /* TABLE */

        .table {
            vertical-align: middle;
        }

        .table thead {
            background-color: #334155;
            color: white;
        }

        /* EMPTY */

        .empty-cart {
            padding: 50px 20px;
            text-align: center;
            color: #64748b;
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


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
            🥬 Toko Sayur
        </a>

        <span class="navbar-text">
            Kasir:
            <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">


        <!-- =================================================
             SIDEBAR
        ================================================== -->

        <div class="col-md-2 sidebar">

            <a href="dashboard.php">
                🏠 Dashboard
            </a>

            <a href="transaksi.php" class="active">
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


        <!-- =================================================
             CONTENT
        ================================================== -->

        <div class="col-md-10 content">


            <div class="mb-4">

                <h3 class="fw-bold mb-1">
                    Transaksi Penjualan
                </h3>

                <p class="text-muted mb-0">
                    Tambahkan produk yang dibeli pelanggan.
                </p>

            </div>


            <!-- =================================================
                 PESAN
            ================================================== -->

            <?php if (isset($_GET['pesan'])): ?>

                <?php if ($_GET['pesan'] == 'tambah'): ?>

                    <div class="alert alert-success">
                        Produk berhasil ditambahkan ke keranjang.
                    </div>

                <?php elseif ($_GET['pesan'] == 'hapus'): ?>

                    <div class="alert alert-success">
                        Produk berhasil dihapus dari keranjang.
                    </div>

                <?php elseif ($_GET['pesan'] == 'kosong'): ?>

                    <div class="alert alert-success">
                        Keranjang berhasil dikosongkan.
                    </div>

                <?php elseif ($_GET['pesan'] == 'stok'): ?>

                    <div class="alert alert-danger">
                        Jumlah produk melebihi stok yang tersedia.
                    </div>

                <?php elseif ($_GET['pesan'] == 'jumlah'): ?>

                    <div class="alert alert-danger">
                        Jumlah produk harus lebih dari 0.
                    </div>

                <?php elseif ($_GET['pesan'] == 'produk'): ?>

                    <div class="alert alert-danger">
                        Produk tidak ditemukan.
                    </div>

                <?php elseif ($_GET['pesan'] == 'uang_kurang'): ?>

                    <div class="alert alert-danger">
                        Uang pembayaran kurang dari total belanja.
                    </div>

                <?php elseif ($_GET['pesan'] == 'gagal'): ?>

                    <div class="alert alert-danger">
                        <?= isset($_GET['error'])
                            ? htmlspecialchars($_GET['error'])
                            : 'Transaksi gagal disimpan.'; ?>
                    </div>

                <?php endif; ?>

            <?php endif; ?>


            <div class="row g-4">


                <!-- =================================================
                     PILIH PRODUK
                ================================================== -->

                <div class="col-lg-5">

                    <div class="card">

                        <div class="card-header">
                            Tambah Produk
                        </div>

                        <div class="card-body">

                            <form method="POST">


                                <!-- PRODUK -->

                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Pilih Produk
                                    </label>

                                    <select
                                        name="id_produk"
                                        id="id_produk"
                                        class="form-select"
                                        required>

                                        <option value="">
                                            -- Pilih Produk --
                                        </option>

                                        <?php while ($produk = mysqli_fetch_assoc($query_produk)): ?>

                                            <option
                                                value="<?= $produk['id_produk']; ?>"
                                                data-harga="<?= $produk['harga']; ?>"
                                                data-stok="<?= $produk['stok']; ?>">

                                                <?= htmlspecialchars($produk['nama_produk']); ?>

                                                -
                                                Rp <?= number_format(
                                                    $produk['harga'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>

                                                | Stok:
                                                <?= $produk['stok']; ?>

                                            </option>

                                        <?php endwhile; ?>

                                    </select>

                                </div>


                                <!-- HARGA -->

                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Harga
                                    </label>

                                    <input
                                        type="text"
                                        id="harga_tampil"
                                        class="form-control"
                                        value="Rp 0"
                                        readonly>

                                </div>


                                <!-- STOK -->

                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Stok Tersedia
                                    </label>

                                    <input
                                        type="text"
                                        id="stok_tampil"
                                        class="form-control"
                                        value="0"
                                        readonly>

                                </div>


                                <!-- JUMLAH -->

                                <div class="mb-4">

                                    <label class="form-label fw-semibold">
                                        Jumlah Produk
                                    </label>

                                    <input
                                        type="number"
                                        name="jumlah"
                                        id="jumlah"
                                        class="form-control"
                                        min="1"
                                        value="1"
                                        required>

                                </div>


                                <button
                                    type="submit"
                                    name="tambah_produk"
                                    class="btn btn-primary-custom w-100">

                                    + Tambah ke Keranjang

                                </button>

                            </form>

                        </div>

                    </div>
                </div>


                <!-- =================================================
                     KERANJANG
                ================================================== -->

                <div class="col-lg-7">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <span>
                                🛒 Keranjang Belanja
                            </span>

                            <?php if (!empty($_SESSION['keranjang'])): ?>

                                <a
                                    href="transaksi.php?kosongkan=1"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Kosongkan semua keranjang?')">

                                    Kosongkan

                                </a>

                            <?php endif; ?>

                        </div>


                        <div class="card-body p-0">

                            <?php if (!empty($_SESSION['keranjang'])): ?>

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover mb-0">

                                        <thead>

                                            <tr>

                                                <th width="50">
                                                    No
                                                </th>

                                                <th>
                                                    Produk
                                                </th>

                                                <th width="90">
                                                    Harga
                                                </th>

                                                <th width="70">
                                                    Qty
                                                </th>

                                                <th>
                                                    Subtotal
                                                </th>

                                                <th width="70">
                                                    Aksi
                                                </th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php

                                            $no = 1;

                                            foreach ($_SESSION['keranjang'] as $item):

                                            ?>

                                                <tr>

                                                    <td>
                                                        <?= $no++; ?>
                                                    </td>

                                                    <td>
                                                        <?= htmlspecialchars(
                                                            $item['nama_produk']
                                                        ); ?>
                                                    </td>

                                                    <td>
                                                        Rp <?= number_format(
                                                            $item['harga'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ); ?>
                                                    </td>

                                                    <td>
                                                        <?= $item['jumlah']; ?>
                                                    </td>

                                                    <td>

                                                        <strong>
                                                            Rp <?= number_format(
                                                                $item['subtotal'],
                                                                0,
                                                                ',',
                                                                '.'
                                                            ); ?>
                                                        </strong>

                                                    </td>

                                                    <td>

                                                        <a
                                                            href="transaksi.php?hapus=<?= $item['id_produk']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Hapus produk ini dari keranjang?')">

                                                            Hapus

                                                        </a>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php else: ?>

                                <div class="empty-cart">

                                    <div style="font-size: 50px;">
                                        🛒
                                    </div>

                                    <h5 class="mt-3">
                                        Keranjang masih kosong
                                    </h5>

                                    <p>
                                        Silakan pilih produk untuk memulai transaksi.
                                    </p>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- =================================================
                         TOTAL
                    ================================================== -->

                    <div class="card mt-4">

                        <div class="card-body">

                            <div class="total-box">

                                <div class="d-flex justify-content-between align-items-center">

                                    <span class="fw-semibold">
                                        Total Belanja
                                    </span>

                                    <span class="total-harga">

                                        Rp <?= number_format(
                                            $total,
                                            0,
                                            ',',
                                            '.'
                                        ); ?>

                                    </span>

                                </div>

                            </div>


                            <?php if (!empty($_SESSION['keranjang'])): ?>

                                <form
                                    action="proses_transaksi.php"
                                    method="POST"
                                    class="mt-4">

                                    <!-- NAMA PEMBELI -->

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Nama Pembeli
                                        </label>

                                        <input
                                            type="text"
                                            name="nama_pelanggan_transaksi"
                                            class="form-control"
                                            placeholder="Masukkan nama pembeli (opsional)"
                                            maxlength="100">

                                        <small class="text-muted">
                                            Kosongkan jika pembeli umum.
                                        </small>

                                    </div>


                                    <!-- TOTAL -->

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Total Pembayaran
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="Rp <?= number_format(
                                                $total,
                                                0,
                                                ',',
                                                '.'
                                            ); ?>"
                                            readonly>

                                    </div>


                                    <!-- UANG BAYAR -->

                                    <div class="mb-4">

                                        <label class="form-label fw-semibold">
                                            Uang Dibayar
                                        </label>

                                        <input
                                            type="number"
                                            name="uang_bayar"
                                            id="uang_bayar"
                                            class="form-control"
                                            min="<?= $total; ?>"
                                            placeholder="Masukkan uang pembayaran"
                                            required>

                                    </div>


                                    <button
                                        type="submit"
                                        name="simpan_transaksi"
                                        class="btn btn-primary-custom w-100">

                                        💰 Simpan Transaksi

                                    </button>

                                </form>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

    // =====================================================
    // TAMPILKAN HARGA DAN STOK SAAT PRODUK DIPILIH
    // =====================================================

    const produkSelect = document.getElementById('id_produk');
    const hargaTampil = document.getElementById('harga_tampil');
    const stokTampil = document.getElementById('stok_tampil');
    const jumlahInput = document.getElementById('jumlah');

    produkSelect.addEventListener('change', function () {

        const selected =
            this.options[this.selectedIndex];

        const harga =
            selected.getAttribute('data-harga');

        const stok =
            selected.getAttribute('data-stok');

        if (harga && stok) {

            hargaTampil.value =
                'Rp ' +
                new Intl.NumberFormat('id-ID').format(harga);

            stokTampil.value = stok;

            jumlahInput.max = stok;

        } else {

            hargaTampil.value = 'Rp 0';

            stokTampil.value = '0';

            jumlahInput.max = '';

        }

    });

</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>