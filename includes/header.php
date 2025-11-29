<?php
// includes/header.php
require_once 'auth.php';
require_once 'functions.php';

// Ambil nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Fungsi untuk memformat Judul Halaman agar tidak "ngeledek"
function getPageTitle($filename) {
    $name = str_replace('.php', '', $filename); // Hapus .php
    $name = str_replace('_', ' ', $name);       // Ganti _ jadi spasi
    
    // Custom nama khusus biar lebih cantik
    if ($name == 'dashboard') return 'DASHBOARD';
    if ($name == 'tambah nasabah') return 'BUAT AKUN NASABAH';
    if ($name == 'data nasabah') return 'DATA NASABAH';
    if ($name == 'nasabah simpanan') return 'SIMPANAN SAYA';
    if ($name == 'nasabah pinjaman') return 'PINJAMAN SAYA';
    if ($name == 'nasabah pembayaran') return 'RIWAYAT PEMBAYARAN';
    
    return strtoupper($name); // Default: Huruf besar semua
}

$page_title = getPageTitle($current_page);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi - <?= ucwords(strtolower($page_title)) ?></title>
    <link rel="stylesheet" href="../css/style.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
</head>
<body class="admin-mode">
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="../images/RBB.jpeg" alt="Logo R33">
            </div>
            <nav class="sidebar-menu">
                
                <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                    <a href="dashboard.php" class="btn-menu <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
                    <a href="tambah_nasabah.php" class="btn-menu <?= $current_page == 'tambah_nasabah.php' ? 'active' : '' ?>">Pembuatan Akun</a>
                    <a href="data_nasabah.php" class="btn-menu <?= $current_page == 'data_nasabah.php' ? 'active' : '' ?>">Data Nasabah</a>
                    <a href="simpanan.php" class="btn-menu <?= $current_page == 'simpanan.php' ? 'active' : '' ?>">Simpanan</a>
                    <a href="pinjaman.php" class="btn-menu <?= $current_page == 'pinjaman.php' ? 'active' : '' ?>">Pinjaman</a>
                    <a href="pembayaran.php" class="btn-menu <?= $current_page == 'pembayaran.php' ? 'active' : '' ?>">Pembayaran</a>
                
                <?php elseif (($_SESSION['role'] ?? '') === 'Nasabah'): ?>
                    <a href="dashboard.php" class="btn-menu <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
                    <a href="profile.php" class="btn-menu <?= $current_page == 'profile.php' ? 'active' : '' ?>">Profile</a>
                    <a href="nasabah_simpanan.php" class="btn-menu <?= $current_page == 'nasabah_simpanan.php' ? 'active' : '' ?>">Simpanan Saya</a>
                    <a href="nasabah_pinjaman.php" class="btn-menu <?= $current_page == 'nasabah_pinjaman.php' ? 'active' : '' ?>">Pinjaman Saya</a>
                    <a href="nasabah_pembayaran.php" class="btn-menu <?= $current_page == 'nasabah_pembayaran.php' ? 'active' : '' ?>">Pembayaran Saya</a>
                
                <?php endif; ?>
                
                <a href="../index.php?logout=true" class="btn-menu btn-logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1><?= $page_title ?></h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?= $_SESSION['user_login'] ?? 'Guest' ?></span>
                            <span class="user-role"><?= $_SESSION['role'] ?? '' ?></span>
                        </div>
                        <div class="user-avatar-circle">
                            <?= strtoupper(substr($_SESSION['user_login'] ?? 'U', 0, 1)) ?>
                        </div>
                    </div>
                </div>
            </header>
            <div class="content-body">