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
// AMBIL DATA PENJUALAN
// =====================================================

$query = mysqli_query(
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

     ORDER BY p.id_penjualan DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Data Penjualan - Admin</title>

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
        }


        /* HEADER */

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h3 {
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

        .table tbody td {
            white-space: nowrap;
        }


        /* BUTTON */

        .btn-detail {
            background-color: #334155;
            color: white;
        }

        .btn-detail:hover {
            background-color: #1e293b;
            color: white;
        }


        /* EMPTY */

        .empty-data {
            padding: 50px;
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


            <a href="../logout.php"
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

                    🧾 Data Penjualan

                </h3>

                <p class="text-muted mb-0">

                    Menampilkan seluruh data transaksi penjualan.

                </p>

            </div>


            <!-- =================================================
                 TABEL PENJUALAN
            ================================================= -->

            <div class="card card-custom">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                                <tr>

                                    <th>No</th>

                                    <th>ID Transaksi</th>

                                    <th>Tanggal</th>

                                    <th>Pelanggan</th>

                                    <th>Kasir</th>

                                    <th>Total</th>

                                    <th>Aksi</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php

                            $no = 1;

                            if (
                                $query &&
                                mysqli_num_rows($query) > 0
                            ):

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

                                        if (
                                            !empty(
                                                $data[
                                                    'nama_pelanggan_transaksi'
                                                ]
                                            )
                                        ) {

                                            echo htmlspecialchars(
                                                $data[
                                                    'nama_pelanggan_transaksi'
                                                ]
                                            );

                                        } elseif (
                                            !empty(
                                                $data[
                                                    'nama_pelanggan'
                                                ]
                                            )
                                        ) {

                                            echo htmlspecialchars(
                                                $data[
                                                    'nama_pelanggan'
                                                ]
                                            );

                                        } else {

                                            echo 'Umum';

                                        }

                                        ?>

                                    </td>


                                    <!-- KASIR -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $data['nama_lengkap']
                                        ); ?>

                                    </td>


                                    <!-- TOTAL -->

                                    <td>

                                        <strong>

                                            Rp
                                            <?= number_format(
                                                $data['total_harga'],
                                                0,
                                                ',',
                                                '.'
                                            ); ?>

                                        </strong>

                                    </td>


                                    <!-- AKSI -->

                                    <td>

                                        <a
                                            href="detail_penjualan.php?id=<?= $data['id_penjualan']; ?>"
                                            class="btn btn-sm btn-detail">

                                            Detail

                                        </a>

                                    </td>


                                </tr>


                            <?php

                                endwhile;

                            else:

                            ?>


                                <tr>

                                    <td
                                        colspan="7"
                                        class="empty-data">

                                        Belum ada data penjualan.

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