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
?>

<div class="window-panel">
    <div class="window-header-strip"><div class="window-icon"></div></div>
    
    <div class="form-wrapper" style="width:100%; max-width:600px; margin: 0 auto; padding-top: 40px;">
        <h3 style="margin-bottom:20px; font-size:20px; color:#333; text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px;">DATA DIRI NASABAH</h3>

        <div style="font-size:16px; line-height:2;">
            
            <div style="padding:8px 0; border-bottom:1px solid #eee;">
                <strong>ID Nasabah:</strong> <?php echo $profile['id_nasabah']; ?>
            </div>

            <div style="padding:8px 0; border-bottom:1px solid #eee;">
                <strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($profile['nama_lengkap']); ?>
            </div>

            <div style="padding:8px 0; border-bottom:1px solid #eee;">
                <strong>Username:</strong> <?php echo htmlspecialchars($profile['username']); ?>
            </div>

            <div style="padding:8px 0; border-bottom:1px solid #eee;">
                <strong>Jabatan:</strong> <?php echo htmlspecialchars($profile['nama_jabatan'] ?? '-'); ?>
            </div>

            <div style="padding:8px 0;">
                <strong>Status Akun:</strong>
                <span style="padding:4px 12px; border-radius:15px; font-weight:bold; color:#fff; font-size: 14px;
                    background:<?php echo ($profile['status']=='AKTIF')?'#28a745':'#dc3545'; ?>;">
                    <?php echo $profile['status']; ?>
                </span>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; 