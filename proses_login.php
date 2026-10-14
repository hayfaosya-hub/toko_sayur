```php
<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi,
    "SELECT * FROM user
     WHERE username='$username'
     AND password='$password'"
);

$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
    $_SESSION['role'] = $data['role'];

    if($data['role'] == 'admin'){
        header("Location: admin/dashboard.php");
    }else{
        header("Location: kasir/dashboard.php");
    }

}else{
    header("Location: login.php?pesan=gagal");
}
?>
```
