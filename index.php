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
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body class="login-page">

    <div class="login-container">
        <div class="logo-area">
           <img src="images/Login.jpg" alt="Logo R33"> 
        </div>

        <form method="POST" action="">
            <div class="form-row">
                <label for="role">Role</label>
                <select name="role" id="role" class="input-role-style" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="Admin">Admin</option>
                    <option value="Nasabah">Nasabah</option>
                </select>
            </div>

            <div class="form-row">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" autocomplete="off" required>
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-login">Login</button>
            </div>
        </form>

        <?php if(!empty($pesan_error)): ?>
            <div class="alert-msg">
                <?php echo $pesan_error; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>