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
// CEK FORM
// =====================================================

if (!isset($_POST['simpan_transaksi'])) {
    header("Location: transaksi.php");
    exit;
}

// =====================================================
// CEK KERANJANG
// =====================================================

if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    header("Location: transaksi.php?pesan=kosong");
    exit;
}

// =====================================================
// DATA TRANSAKSI
// =====================================================

$id_user = (int) $_SESSION['id_user'];

$nama_pelanggan = isset($_POST['nama_pelanggan_transaksi'])
    ? trim($_POST['nama_pelanggan_transaksi'])
    : '';


// Jika kosong, simpan NULL
if ($nama_pelanggan == '') {
    $nama_pelanggan = NULL;
} else {
    $nama_pelanggan = mysqli_real_escape_string(
        $koneksi,
        $nama_pelanggan
    );
}


// =====================================================
// UANG BAYAR
// =====================================================

$uang_bayar = isset($_POST['uang_bayar'])
    ? (int) $_POST['uang_bayar']
    : 0;


// =====================================================
// HITUNG TOTAL
// =====================================================

$total_harga = 0;

foreach ($_SESSION['keranjang'] as $item) {

    $jumlah = (int) $item['jumlah'];
    $harga = (int) $item['harga'];

    $total_harga += $jumlah * $harga;
}


// =====================================================
// CEK UANG
// =====================================================

if ($uang_bayar < $total_harga) {

    header("Location: transaksi.php?pesan=uang_kurang");
    exit;
}


// =====================================================
// HITUNG KEMBALIAN
// =====================================================

$kembalian = $uang_bayar - $total_harga;


// =====================================================
// MULAI TRANSAKSI DATABASE
// =====================================================

mysqli_begin_transaction($koneksi);

try {

    // =================================================
    // CEK STOK
    // =================================================

    foreach ($_SESSION['keranjang'] as $item) {

        $id_produk = (int) $item['id_produk'];
        $jumlah = (int) $item['jumlah'];

        $query_stok = mysqli_query(
            $koneksi,
            "SELECT nama_produk, stok, harga
             FROM produk
             WHERE id_produk = '$id_produk'
             FOR UPDATE"
        );

        if (!$query_stok) {
            throw new Exception(
                "Gagal mengecek stok produk."
            );
        }

        $data_produk = mysqli_fetch_assoc($query_stok);

        if (!$data_produk) {
            throw new Exception(
                "Produk tidak ditemukan."
            );
        }

        // Cek stok
        if ($jumlah > $data_produk['stok']) {

            throw new Exception(
                "Stok produk " .
                $data_produk['nama_produk'] .
                " tidak mencukupi."
            );
        }

        // Gunakan harga terbaru dari database
        $_SESSION['keranjang'][$id_produk]['harga']
            = $data_produk['harga'];

        $_SESSION['keranjang'][$id_produk]['subtotal']
            = $jumlah * $data_produk['harga'];
    }


    // =================================================
    // HITUNG ULANG TOTAL
    // =================================================

    $total_harga = 0;

    foreach ($_SESSION['keranjang'] as $item) {

        $jumlah = (int) $item['jumlah'];
        $harga = (int) $item['harga'];

        $total_harga += $jumlah * $harga;
    }


    // =================================================
    // CEK UANG LAGI
    // =================================================

    if ($uang_bayar < $total_harga) {

        throw new Exception(
            "Uang pembayaran kurang."
        );
    }


    // =================================================
    // HITUNG KEMBALIAN
    // =================================================

    $kembalian = $uang_bayar - $total_harga;


    // =================================================
    // SIMPAN / CEK DATA PELANGGAN
    // =================================================

    $id_pelanggan = NULL;

    if ($nama_pelanggan !== NULL && $nama_pelanggan !== '') {

        // Cek apakah pelanggan sudah ada
        $query_cek_pelanggan = mysqli_query(
            $koneksi,
            "SELECT id_pelanggan
            FROM pelanggan
            WHERE nama_pelanggan = '$nama_pelanggan'
            LIMIT 1"
        );

        if (!$query_cek_pelanggan) {
            throw new Exception(
                "Gagal mengecek data pelanggan: " .
                mysqli_error($koneksi)
            );
        }

        $data_pelanggan = mysqli_fetch_assoc($query_cek_pelanggan);

        if ($data_pelanggan) {

            // Pelanggan sudah ada
            $id_pelanggan = $data_pelanggan['id_pelanggan'];

        } else {

            // Pelanggan belum ada, tambahkan
            $query_tambah_pelanggan = mysqli_query(
                $koneksi,
                "INSERT INTO pelanggan (nama_pelanggan)
                VALUES ('$nama_pelanggan')"
            );

            if (!$query_tambah_pelanggan) {
                throw new Exception(
                    "Gagal menambahkan pelanggan: " .
                    mysqli_error($koneksi)
                );
            }

            $id_pelanggan = mysqli_insert_id($koneksi);
        }
    }
    // =================================================
    // SIMPAN PENJUALAN
    // =================================================

        $query_penjualan = mysqli_query(
        $koneksi,
        "INSERT INTO penjualan
        (
            tgl_penjualan,
            total_harga,
            id_pelanggan,
            nama_pelanggan_transaksi,
            id_user
        )
        VALUES
        (
            NOW(),
            '$total_harga',
            " . ($id_pelanggan === NULL ? "NULL" : "'$id_pelanggan'") . ",
            " . ($nama_pelanggan === NULL ? "NULL" : "'$nama_pelanggan'") . ",
            '$id_user'
        )"
    );


    // =================================================
    // CEK PENJUALAN
    // =================================================

    if (!$query_penjualan) {

        throw new Exception(
            "Gagal menyimpan transaksi: " .
            mysqli_error($koneksi)
        );
    }


    // =================================================
    // AMBIL ID PENJUALAN
    // =================================================

    $id_penjualan = mysqli_insert_id($koneksi);


    // =================================================
    // SIMPAN DETAIL & KURANGI STOK
    // =================================================

    foreach ($_SESSION['keranjang'] as $item) {

        $id_produk = (int) $item['id_produk'];
        $jumlah = (int) $item['jumlah'];
        $harga = (int) $item['harga'];

        $subtotal = $jumlah * $harga;


        // ---------------------------------------------
        // SIMPAN DETAIL
        // ---------------------------------------------

        $query_detail = mysqli_query(
            $koneksi,
            "INSERT INTO detail_penjualan
            (
                id_penjualan,
                id_produk,
                jumlah_produk,
                subtotal
            )
            VALUES
            (
                '$id_penjualan',
                '$id_produk',
                '$jumlah',
                '$subtotal'
            )"
        );


        if (!$query_detail) {

            throw new Exception(
                "Gagal menyimpan detail transaksi: " .
                mysqli_error($koneksi)
            );
        }


        // ---------------------------------------------
        // KURANGI STOK
        // ---------------------------------------------

        $query_update_stok = mysqli_query(
            $koneksi,
            "UPDATE produk
             SET stok = stok - '$jumlah'
             WHERE id_produk = '$id_produk'"
        );


        if (!$query_update_stok) {

            throw new Exception(
                "Gagal mengurangi stok produk."
            );
        }
    }


    // =================================================
    // COMMIT
    // =================================================

    mysqli_commit($koneksi);


    // =================================================
    // SIMPAN DATA UNTUK STRUK
    // =================================================

    $_SESSION['transaksi_sukses'] = [

        'id_penjualan' => $id_penjualan,

        'total_harga' => $total_harga,

        'uang_bayar' => $uang_bayar,

        'kembalian' => $kembalian,

        'nama_pelanggan' =>
            $nama_pelanggan === NULL
            ? 'Pelanggan Umum'
            : $nama_pelanggan
    ];


    // =================================================
    // KOSONGKAN KERANJANG
    // =================================================

    $_SESSION['keranjang'] = [];


    // =================================================
    // KE STRUK
    // =================================================

    header(
        "Location: struk.php?id=" . $id_penjualan
    );

    exit;


} catch (Exception $e) {

    // =================================================
    // ROLLBACK
    // =================================================

    mysqli_rollback($koneksi);

    $pesan = urlencode($e->getMessage());

    header(
        "Location: transaksi.php?pesan=gagal&error=" . $pesan
    );

    exit;
}

?>