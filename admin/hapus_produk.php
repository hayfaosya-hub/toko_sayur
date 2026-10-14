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

// Cek ID produk
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id_produk = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

// Hapus produk
$query = mysqli_query(
    $koneksi,
    "DELETE FROM produk
     WHERE id_produk='$id_produk'"
);

// Jika berhasil
if ($query) {

    header("Location: produk.php?pesan=hapus");
    exit;

} else {

    // Jika gagal, kembali ke halaman produk
    header("Location: produk.php?pesan=gagal_hapus");
    exit;

}
?>