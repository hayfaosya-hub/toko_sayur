<?php
session_start();

if(isset($_SESSION['id_user'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: admin/dashboard.php");
    }else{
        header("Location: kasir/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Sayur</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header text-center bg-success text-white">
                    <h4>Login Toko Sayur</h4>
                </div>

                <div class="card-body">

                    <?php
                    if(isset($_GET['pesan'])){
                        echo '<div class="alert alert-danger">Username atau Password salah!</div>';
                    }
                    ?>

                    <form action="proses_login.php" method="POST">

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            Login
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>