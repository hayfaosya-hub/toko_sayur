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

// Cek ID pelanggan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pelanggan.php");
    exit;
}

$id_pelanggan = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

// Hapus pelanggan
$query = mysqli_query(
    $koneksi,
    "DELETE FROM pelanggan
     WHERE id_pelanggan='$id_pelanggan'"
);

if ($query) {

    header("Location: pelanggan.php?pesan=hapus");
    exit;

} else {

    // Kemungkinan gagal karena pelanggan sudah digunakan
    // pada tabel penjualan
    header("Location: pelanggan.php?pesan=gagal_hapus");
    exit;

}

?>