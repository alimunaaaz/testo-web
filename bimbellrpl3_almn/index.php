<?php
include 'koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass_input = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Cek di tabel siswa (Huruf kecil)
    $qSiswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$user_input' OR username='$user_input'");
    $dataSiswa = mysqli_fetch_assoc($qSiswa);

    if ($dataSiswa) {
        if (empty($dataSiswa['password']) || $pass_input == $dataSiswa['password']) {
            $_SESSION['role'] = 'siswa';
            $_SESSION['nis'] = $dataSiswa['nis'];
            $_SESSION['nama'] = $dataSiswa['nama_siswa'];
            header("location:siswa.php");
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location='index.php';</script>";
            exit();
        }
    } else {
        // Cek di tabel admin (Huruf kecil)
        $qAdmin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$user_input' AND password='$pass_input'");
        $dataAdmin = mysqli_fetch_assoc($qAdmin);
        if ($dataAdmin) {
            $_SESSION['role'] = 'admin';
            $_SESSION['user'] = $dataAdmin['username'];
            header("location:admin.php");
            exit();
        } else {
            echo "<script>alert('Akun tidak ditemukan!'); window.location='index.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa | E-Aspirasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                url('https://wallpapercave.com/wp/wp4538073.jpg');
            background-size: cover;
            background-position: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: white;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .subtitle {
            opacity: 0.8;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-box label {
            font-size: 12px;
            margin-left: 15px;
            margin-bottom: 5px;
            display: block;
            text-transform: uppercase;
        }

        .input-box input {
            width: 100%;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            color: white;
            outline: none;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h1>Login Siswa</h1>
        <p class="subtitle">Silakan masukkan NIS atau Username Anda</p>

        <form method="POST">
            <div class="input-box">
                <label>NIS / Username</label>
                <input type="text" name="username" placeholder="Masukkan NIS Anda" required autocomplete="off">
            </div>

            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" placeholder="Kosongkan jika siswa baru">
            </div>

            <button type="submit" name="login" class="btn-login">MASUK</button>
        </form>
    </div>

</body>

</html>