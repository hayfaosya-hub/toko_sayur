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

$id_user = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

// Pastikan user yang akan dihapus adalah kasir
$cek = mysqli_query(
    $koneksi,
    "SELECT * FROM user
     WHERE id_user='$id_user'
     AND role='kasir'"
);

if (mysqli_num_rows($cek) == 0) {

    header("Location: user.php?pesan=gagal_hapus");
    exit;

}

// Hapus kasir
$query = mysqli_query(
    $koneksi,
    "DELETE FROM user
     WHERE id_user='$id_user'
     AND role='kasir'"
);

// Cek hasil
if ($query) {

    header("Location: user.php?pesan=hapus");
    exit;

} else {

    header("Location: user.php?pesan=gagal_hapus");
    exit;

}

?>