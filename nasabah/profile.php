<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginNasabah(); 

$id_saya = $_SESSION['user_id']; 

$query = "SELECT nasabah.*, jabatan.nama_jabatan 
          FROM nasabah 
          LEFT JOIN jabatan ON nasabah.id_jabatan = jabatan.id_jabatan 
          WHERE nasabah.id_nasabah = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_saya]);
$profile = $stmt->fetch(); 

if (empty($profile)) {
    die("Data profil tidak ditemukan.");
}

// Initials for Avatar
$initials = strtoupper(substr($profile['nama_lengkap'], 0, 1));
?>

<div class="dashboard-content">
    <div class="dashboard-welcome">
        <h2>👤 Kelola informasi data diri anda</h2>
    </div>

    <div class="profile-layout">
        
        <!-- Left Sidebar: Avatar & Short Info -->
        <div class="profile-sidebar">
            <div class="profile-avatar-large">
                <?php echo $initials; ?>
            </div>
            <div class="profile-name"><?php echo htmlspecialchars($profile['nama_lengkap']); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($profile['nama_jabatan'] ?? 'Nasabah'); ?></div>
            
            <div style="margin-top: 10px;">
                <span class="status-badge <?php echo ($profile['status']=='AKTIF')?'badge-success':'badge-danger'; ?>">
                    <?php echo $profile['status']; ?>
                </span>
            </div>
        </div>

        <!-- Right Main: Detailed Info -->
        <div class="profile-main">
            <div class="profile-section-title">
                <span>📄 Detail Informasi</span>
            </div>

            <div class="profile-grid">
                <div class="profile-item">
                    <label>ID Nasabah</label>
                    <div><?php echo $profile['id_nasabah']; ?></div>
                </div>

                <div class="profile-item">
                    <label>Username</label>
                    <div><?php echo htmlspecialchars($profile['username']); ?></div>
                </div>

                <div class="profile-item">
                    <label>Nama Lengkap</label>
                    <div><?php echo htmlspecialchars($profile['nama_lengkap']); ?></div>
                </div>

                <div class="profile-item">
                    <label>Jabatan</label>
                    <div><?php echo htmlspecialchars($profile['nama_jabatan'] ?? '-'); ?></div>
                </div>

                <div class="profile-item">
                    <label>Tanggal Bergabung</label>
                    <div>-</div> <!-- Bisa ditambahkan kolom tgl_daftar jika ada -->
                </div>

                <div class="profile-item">
                    <label>Status Akun</label>
                    <div style="color: var(--success-color);"><?php echo $profile['status']; ?></div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>