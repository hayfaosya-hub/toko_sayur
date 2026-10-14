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
// CEK ROLE
// =====================================================

if ($_SESSION['role'] != 'kasir') {
    header("Location: ../login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];


// =====================================================
// AMBIL RIWAYAT TRANSAKSI KASIR
// =====================================================

$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id_penjualan,
        p.tgl_penjualan,
        p.total_harga,
        p.id_pelanggan,
        p.nama_pelanggan_transaksi,
        pl.nama_pelanggan
     FROM penjualan p
     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan
     WHERE p.id_user = '$id_user'
     ORDER BY p.id_penjualan DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Riwayat Penjualan - Toko Sayur</title>

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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
            padding: 18px 20px;
        }


        /* TABLE */

        .table {
            vertical-align: middle;
        }

        .table thead {
            background-color: #334155;
            color: white;
        }


        /* BUTTON */

        .btn-detail {
            background-color: #334155;
            color: white;
            border: none;
        }

        .btn-detail:hover {
            background-color: #1e293b;
            color: white;
        }


        /* EMPTY */

        .empty-data {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
        }


        /* TOTAL */

        .total-transaksi {
            font-weight: bold;
            color: #1e293b;
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

            <a href="transaksi.php">
                🛒 Transaksi
            </a>

            <a href="riwayat.php" class="active">
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


            <!-- HEADER -->

            <div class="mb-4">

                <h3 class="fw-bold mb-1">
                    Riwayat Penjualan
                </h3>

                <p class="text-muted mb-0">

                    Daftar transaksi yang telah dilakukan.

                </p>

            </div>


            <!-- =================================================
                 CARD RIWAYAT
            ================================================== -->

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <span>
                        🧾 Data Transaksi
                    </span>

                    <a
                        href="transaksi.php"
                        class="btn btn-dark btn-sm">

                        + Transaksi Baru

                    </a>

                </div>


                <div class="card-body p-0">


                    <?php if (mysqli_num_rows($query) > 0): ?>


                        <div class="table-responsive">

                            <table class="table table-bordered table-hover mb-0">

                                <thead>

                                    <tr>

                                        <th width="60">
                                            No
                                        </th>

                                        <th>
                                            ID Transaksi
                                        </th>

                                        <th>
                                            Tanggal
                                        </th>

                                        <th>
                                            Pelanggan
                                        </th>

                                        <th>
                                            Total Harga
                                        </th>

                                        <th width="120">
                                            Aksi
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                    <?php

                                    $no = 1;

                                    while (
                                        $data =
                                        mysqli_fetch_assoc($query)
                                    ):

                                    ?>


                                        <tr>

                                            <!-- NO -->

                                            <td>
                                                <?= $no++; ?>
                                            </td>


                                            <!-- ID -->

                                            <td>

                                                <strong>

                                                    #<?= $data['id_penjualan']; ?>

                                                </strong>

                                            </td>


                                            <!-- TANGGAL -->

                                            <td>

                                                <?= date(
                                                    'd-m-Y H:i',
                                                    strtotime(
                                                        $data['tgl_penjualan']
                                                    )
                                                ); ?>

                                            </td>


                                            <!-- PELANGGAN -->

                                            <td>

                                                <?php

                                                if (!empty($data['nama_pelanggan_transaksi'])) {

                                                    echo htmlspecialchars(
                                                        $data['nama_pelanggan_transaksi']
                                                    );

                                                } elseif (!empty($data['nama_pelanggan'])) {

                                                    echo htmlspecialchars(
                                                        $data['nama_pelanggan']
                                                    );

                                                } else {

                                                    echo "Umum";

                                                }

                                                ?>

                                            </td>


                                            <!-- TOTAL -->

                                            <td class="total-transaksi">

                                                Rp
                                                <?= number_format(
                                                    $data['total_harga'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>

                                            </td>


                                            <!-- AKSI -->

                                            <td>

                                                <a
                                                    href="struk.php?id=<?= $data['id_penjualan']; ?>"
                                                    class="btn btn-detail btn-sm w-100">

                                                    Lihat Struk

                                                </a>

                                            </td>

                                        </tr>


                                    <?php endwhile; ?>


                                </tbody>

                            </table>

                        </div>


                    <?php else: ?>


                        <!-- DATA KOSONG -->

                        <div class="empty-data">

                            <div style="font-size: 50px;">
                                🧾
                            </div>

                            <h5 class="mt-3">
                                Belum Ada Transaksi
                            </h5>

                            <p>
                                Belum ada transaksi penjualan yang dilakukan oleh Anda.
                            </p>

                            <a
                                href="transaksi.php"
                                class="btn btn-dark">

                                Mulai Transaksi

                            </a>

                        </div>


                    <?php endif; ?>


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