<?php
session_start();
require_once 'config/database.php';

$pesan_error = "";

// Fitur Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role     = $_POST['role'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($role) || empty($username) || empty($password)) {
        $pesan_error = "Role, Username, dan Password wajib diisi!";
    } else {
        $user = null;
        
        if ($role === 'Admin') {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
        } else if ($role === 'Nasabah') {
            $stmt = $pdo->prepare("SELECT * FROM nasabah WHERE username = :username");
        } else {
            $pesan_error = "Role tidak valid.";
            goto end_login;
        }
        
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            // DETEKSI KOLOM PASSWORD (Bisa 'password' atau 'hashed_password')
            $db_password = isset($user['hashed_password']) ? $user['hashed_password'] : (isset($user['password']) ? $user['password'] : '');

            // Cek Password (Plain text)
            if ($password === $db_password) {
                
                $_SESSION['user_login'] = $user['username'];
                $_SESSION['role'] = $role;

                if($role == 'Admin') {
                    $_SESSION['user_id'] = $user['id_admin'];
                    header("Location: admin/dashboard.php"); 
                } else {
                    $_SESSION['user_id'] = $user['id_nasabah'];
                    if(isset($user['status']) && $user['status'] == 'SUSPEND'){
                        session_destroy();
                        $pesan_error = "Akun Anda ditangguhkan (Suspend).";
                    } else {
                        header("Location: nasabah/dashboard.php");
                    }
                }
                exit;

            } else {
                $pesan_error = "Password salah!";
            }
        } else {
            $pesan_error = "Username tidak ditemukan di role $role!";
        }
    }
    end_login:
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Koperasi</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>"> 
</head>
<body class="login-page">

    <div class="login-split-wrapper">
        
        <!-- Left Side: Hero/Image -->
        <div class="login-hero">
            <div class="hero-content">
                <h1>SISTEM INFORMASI MANAJEMEN</h1>
                <p>Sistem Informasi Simpan Pinjam PT Rampai Berkah Bersama</p>
                <span> Hak Cipta © 2025 by 
                    <br>1. Adrian Yudhaswara
                    <br>2. Andika Galih Pangestu
<br> 3. Andika Daffa Fathi Rabbani 
</span>
            </div>
            <div class="hero-overlay"></div>
        </div>

        <!-- Right Side: Form -->
        <div class="login-form-container">
            <div class="login-card-content">
                <div class="login-header">
                    <div class="logo-login">
                        <img src="images/Login.jpg" alt="Logo">
                    </div>
                    <h2>Selamat Datang</h2>
                    <p>Silakan login untuk melanjutkan</p>
                </div>

                <form method="POST" action="" class="login-form">
                    
                    <div class="form-group">
                        <label for="role">Masuk Sebagai</label>
                        <div class="select-wrapper">
                            <select name="role" id="role" required>
                                <option value="" disabled selected>Pilih Role...</option>
                                <option value="Admin">Admin</option>
                                <option value="Nasabah">Nasabah</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Masukkan username" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn-login-modern">
                        Masuk Aplikasi <span class="arrow">→</span>
                    </button>

                </form>

                <?php if(!empty($pesan_error)): ?>
                    <div class="login-alert">
                        <span class="icon">⚠️</span> <?php echo $pesan_error; ?>
                    </div>
                <?php endif; ?>

                <div class="login-footer">
                    <p>&copy; <?= date('Y') ?> PT Rampai Berkah Bersama</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>