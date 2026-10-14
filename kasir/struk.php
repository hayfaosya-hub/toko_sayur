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

// =====================================================
// CEK ID TRANSAKSI
// =====================================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: transaksi.php");
    exit;
}

$id_penjualan = (int) $_GET['id'];


// =====================================================
// AMBIL DATA PENJUALAN
// =====================================================

$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT
        p.id_penjualan,
        p.tgl_penjualan,
        p.total_harga,
        p.id_pelanggan,
        p.nama_pelanggan_transaksi,
        p.id_user,
        u.nama_lengkap,
        pl.nama_pelanggan,
        pl.no_telp
     FROM penjualan p
     LEFT JOIN user u
        ON p.id_user = u.id_user
     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan
     WHERE p.id_penjualan = '$id_penjualan'"
);

$data_penjualan = mysqli_fetch_assoc($query_penjualan);


// =====================================================
// JIKA TRANSAKSI TIDAK DITEMUKAN
// =====================================================

if (!$data_penjualan) {
    echo "Transaksi tidak ditemukan.";
    exit;
}


// =====================================================
// PASTIKAN KASIR HANYA MELIHAT TRANSAKSINYA SENDIRI
// =====================================================

if (
    $_SESSION['role'] == 'kasir' &&
    $data_penjualan['id_user'] != $_SESSION['id_user']
) {
    echo "Anda tidak memiliki akses ke transaksi ini.";
    exit;
}


// =====================================================
// AMBIL DETAIL PENJUALAN
// =====================================================

$query_detail = mysqli_query(
    $koneksi,
    "SELECT
        d.id_detail,
        d.id_produk,
        d.jumlah_produk,
        d.subtotal,
        pr.nama_produk,
        pr.harga
     FROM detail_penjualan d
     LEFT JOIN produk pr
        ON d.id_produk = pr.id_produk
     WHERE d.id_penjualan = '$id_penjualan'
     ORDER BY d.id_detail ASC"
);


// =====================================================
// DATA PEMBAYARAN DARI SESSION
// =====================================================

$uang_bayar = 0;
$kembalian = 0;

if (
    isset($_SESSION['transaksi_sukses']) &&
    $_SESSION['transaksi_sukses']['id_penjualan'] == $id_penjualan
) {

    $uang_bayar =
        $_SESSION['transaksi_sukses']['uang_bayar'];

    $kembalian =
        $_SESSION['transaksi_sukses']['kembalian'];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Struk Transaksi #<?= $id_penjualan; ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <style>

        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        .struk-container {
            max-width: 600px;
            margin: 40px auto;
        }

        .struk {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.10);
        }

        .header-struk {
            text-align: center;
            border-bottom: 2px dashed #64748b;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header-struk h2 {
            font-weight: bold;
            margin-bottom: 5px;
            color: #1e293b;
        }

        .header-struk p {
            margin: 2px 0;
            color: #64748b;
        }

        .info-transaksi {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .info-label {
            color: #64748b;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
        }

        .table {
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table th {
            background-color: #334155;
            color: white;
            font-size: 14px;
        }

        .table td {
            font-size: 14px;
        }

        .total-section {
            border-top: 2px dashed #64748b;
            margin-top: 20px;
            padding-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .footer-struk {
            text-align: center;
            border-top: 2px dashed #64748b;
            margin-top: 20px;
            padding-top: 20px;
            color: #64748b;
        }

        .button-area {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .btn-dark-custom {
            background-color: #334155;
            color: white;
            border: none;
        }

        .btn-dark-custom:hover {
            background-color: #1e293b;
            color: white;
        }


        /* =================================================
           CETAK
        ================================================== */

        @media print {

            body {
                background-color: white;
            }

            .struk-container {
                max-width: 100%;
                margin: 0;
            }

            .struk {
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
            }

            .button-area {
                display: none;
            }

            .no-print {
                display: none !important;
            }

        }

    </style>

</head>


<body>


<div class="container struk-container">


    <!-- =================================================
         STRUK
    ================================================== -->

    <div class="struk">


        <!-- HEADER -->

        <div class="header-struk">

            <h2>
                🥬 TOKO SAYUR
            </h2>

            <p>
                Sistem Kasir Toko Sayur
            </p>

            <p>
                Terima kasih telah berbelanja
            </p>

        </div>


        <!-- =================================================
             INFORMASI TRANSAKSI
        ================================================== -->

        <div class="info-transaksi">

            <div class="info-row">

                <span class="info-label">
                    ID Transaksi
                </span>

                <span class="info-value">
                    #<?= $data_penjualan['id_penjualan']; ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Tanggal
                </span>

                <span class="info-value">

                    <?= date(
                        'd-m-Y H:i',
                        strtotime($data_penjualan['tgl_penjualan'])
                    ); ?>

                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Kasir
                </span>

                <span class="info-value">

                    <?= htmlspecialchars(
                        $data_penjualan['nama_lengkap']
                    ); ?>

                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Pelanggan
                </span>

                <span class="info-value">

                    <?php

                    if (!empty($data_penjualan['nama_pelanggan_transaksi'])) {

                        echo htmlspecialchars(
                            $data_penjualan['nama_pelanggan_transaksi']
                        );

                    } elseif (!empty($data_penjualan['nama_pelanggan'])) {

                        echo htmlspecialchars(
                            $data_penjualan['nama_pelanggan']
                        );

                    } else {

                        echo "Umum";

                    }

                    ?>

                </span>

            </div>


        <!-- =================================================
             DAFTAR PRODUK
        ================================================== -->

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>
                            Produk
                        </th>

                        <th class="text-center">
                            Qty
                        </th>

                        <th class="text-end">
                            Harga
                        </th>

                        <th class="text-end">
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    while (
                        $detail =
                        mysqli_fetch_assoc($query_detail)
                    ):

                    ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $detail['nama_produk']
                                ); ?>
                            </td>

                            <td class="text-center">
                                <?= $detail['jumlah_produk']; ?>
                            </td>

                            <td class="text-end">

                                Rp <?= number_format(
                                    $detail['harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </td>

                            <td class="text-end">

                                Rp <?= number_format(
                                    $detail['subtotal'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>


        <!-- =================================================
             TOTAL
        ================================================== -->

        <div class="total-section">


            <div class="total-row grand-total">

                <span>
                    TOTAL
                </span>

                <span>

                    Rp <?= number_format(
                        $data_penjualan['total_harga'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </span>

            </div>


            <div class="payment-row">

                <span>
                    Uang Bayar
                </span>

                <span>

                    Rp <?= number_format(
                        $uang_bayar,
                        0,
                        ',',
                        '.'
                    ); ?>

                </span>

            </div>


            <div class="payment-row">

                <span>
                    Kembalian
                </span>

                <span>

                    Rp <?= number_format(
                        $kembalian,
                        0,
                        ',',
                        '.'
                    ); ?>

                </span>

            </div>


        </div>


        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="footer-struk">

            <strong>
                Terima Kasih 🙏
            </strong>

            <p class="mb-0 mt-2">
                Barang yang sudah dibeli tidak dapat dikembalikan.
            </p>

        </div>


        <!-- =================================================
             BUTTON
        ================================================== -->

        <div class="button-area no-print">

            <button
                onclick="window.print()"
                class="btn btn-dark-custom flex-fill">

                🖨️ Cetak Struk

            </button>


            <a
                href="transaksi.php"
                class="btn btn-secondary flex-fill">

                🛒 Transaksi Baru

            </a>

        </div>


    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>