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
// CEK ROLE ADMIN
// =====================================================

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// =====================================================
// CEK ID TRANSAKSI
// =====================================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: penjualan.php");
    exit;
}

$id_penjualan = (int) $_GET['id'];


// =====================================================
// AMBIL DATA TRANSAKSI
// =====================================================

$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT
        p.id_penjualan,
        p.tgl_penjualan,
        p.total_harga,
        p.nama_pelanggan_transaksi,
        pl.nama_pelanggan,
        u.nama_lengkap
     FROM penjualan p

     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan

     LEFT JOIN user u
        ON p.id_user = u.id_user

     WHERE p.id_penjualan = '$id_penjualan'
     LIMIT 1"
);

if (!$query_penjualan) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$data_penjualan = mysqli_fetch_assoc($query_penjualan);


// =====================================================
// CEK TRANSAKSI ADA ATAU TIDAK
// =====================================================

if (!$data_penjualan) {
    header("Location: penjualan.php");
    exit;
}


// =====================================================
// AMBIL DETAIL PRODUK
// =====================================================

$query_detail = mysqli_query(
    $koneksi,
    "SELECT
        dp.id_detail,
        dp.jumlah_produk,
        dp.subtotal,
        pr.nama_produk,
        pr.harga
     FROM detail_penjualan dp

     INNER JOIN produk pr
        ON dp.id_produk = pr.id_produk

     WHERE dp.id_penjualan = '$id_penjualan'
     ORDER BY dp.id_detail ASC"
);

if (!$query_detail) {
    die("Query detail gagal: " . mysqli_error($koneksi));
}


// =====================================================
// NAMA PELANGGAN
// =====================================================

if (!empty($data_penjualan['nama_pelanggan_transaksi'])) {

    $nama_pelanggan =
        $data_penjualan['nama_pelanggan_transaksi'];

} elseif (!empty($data_penjualan['nama_pelanggan'])) {

    $nama_pelanggan =
        $data_penjualan['nama_pelanggan'];

} else {

    $nama_pelanggan = 'Umum';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Detail Penjualan #<?= $data_penjualan['id_penjualan']; ?>
    </title>

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

        .navbar-brand,
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

        .card-custom {
            background-color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }


        /* HEADER */

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h3 {
            color: #1e293b;
            font-weight: bold;
        }


        /* INFO */

        .info-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .info-value {
            color: #1e293b;
            font-weight: bold;
        }


        /* TABLE */

        .table {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #334155;
            color: white;
            white-space: nowrap;
        }


        /* TOTAL */

        .total-box {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px 20px;
        }

        .total-label {
            font-size: 18px;
            font-weight: bold;
        }

        .total-value {
            font-size: 22px;
            font-weight: bold;
            color: #1e293b;
        }


        /* BUTTON */

        .btn-kembali {
            background-color: #334155;
            color: white;
            border: none;
        }

        .btn-kembali:hover {
            background-color: #1e293b;
            color: white;
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

<nav class="navbar navbar-expand-lg">

    <div class="container-fluid">

        <a
            class="navbar-brand fw-bold"
            href="dashboard.php">

            🥬 Toko Sayur

        </a>

        <span class="navbar-text">

            Admin:
            <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>

        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">


        <!-- =================================================
             SIDEBAR
        ================================================= -->

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

            <a href="pelanggan.php">

                👥 Data Pelanggan

            </a>

            <a href="penjualan.php"
               class="active">

                🧾 Data Penjualan

            </a>

            <a href="laporan.php">

                📊 Laporan

            </a>

            <a
                href="../logout.php"
                onclick="return confirm('Apakah Anda yakin ingin logout?')">

                🚪 Logout

            </a>

        </div>


        <!-- =================================================
             CONTENT
        ================================================= -->

        <div class="col-md-10 content">


            <!-- HEADER -->

            <div class="page-header">

                <h3>

                    🧾 Detail Penjualan

                </h3>

                <p class="text-muted mb-0">

                    Detail transaksi
                    #<?= $data_penjualan['id_penjualan']; ?>

                </p>

            </div>


            <!-- =================================================
                 INFORMASI TRANSAKSI
            ================================================= -->

            <div class="card card-custom">

                <div class="card-body">

                    <div class="row g-4">


                        <!-- ID TRANSAKSI -->

                        <div class="col-md-3">

                            <div class="info-label">
                                ID Transaksi
                            </div>

                            <div class="info-value">

                                #<?= $data_penjualan['id_penjualan']; ?>

                            </div>

                        </div>


                        <!-- TANGGAL -->

                        <div class="col-md-3">

                            <div class="info-label">
                                Tanggal
                            </div>

                            <div class="info-value">

                                <?= date(
                                    'd-m-Y H:i',
                                    strtotime(
                                        $data_penjualan['tgl_penjualan']
                                    )
                                ); ?>

                            </div>

                        </div>


                        <!-- PELANGGAN -->

                        <div class="col-md-3">

                            <div class="info-label">
                                Pelanggan
                            </div>

                            <div class="info-value">

                                <?= htmlspecialchars(
                                    $nama_pelanggan
                                ); ?>

                            </div>

                        </div>


                        <!-- KASIR -->

                        <div class="col-md-3">

                            <div class="info-label">
                                Kasir
                            </div>

                            <div class="info-value">

                                <?= htmlspecialchars(
                                    $data_penjualan['nama_lengkap']
                                ); ?>

                            </div>

                        </div>


                    </div>

                </div>

            </div>


            <!-- =================================================
                 DETAIL PRODUK
            ================================================= -->

            <div class="card card-custom">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">

                        Daftar Produk

                    </h5>


                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                                <tr>

                                    <th>No</th>

                                    <th>Produk</th>

                                    <th>Harga</th>

                                    <th>Jumlah</th>

                                    <th>Subtotal</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php

                            $no = 1;

                            if (
                                mysqli_num_rows($query_detail) > 0
                            ):

                                while (
                                    $detail =
                                    mysqli_fetch_assoc(
                                        $query_detail
                                    )
                                ):

                            ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $detail['nama_produk']
                                        ); ?>
                                    </td>

                                    <td>
                                        Rp
                                        <?= number_format(
                                            $detail['harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= $detail['jumlah_produk']; ?>
                                    </td>

                                    <td>
                                        <strong>
                                            Rp
                                            <?= number_format(
                                                $detail['subtotal'],
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

                                    <td
                                        colspan="5"
                                        class="text-center text-muted py-4">

                                        Tidak ada detail produk.

                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- TOTAL -->

                    <div class="total-box mt-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <span class="total-label">

                                Total Penjualan

                            </span>

                            <span class="total-value">

                                Rp
                                <?= number_format(
                                    $data_penjualan['total_harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </span>

                        </div>

                    </div>


                </div>

            </div>


            <!-- KEMBALI -->

            <a
                href="penjualan.php"
                class="btn btn-kembali">

                ← Kembali ke Data Penjualan

            </a>


        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>