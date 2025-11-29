<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginNasabah(); 

$id_nasabah = $_SESSION['user_id'];
$pesan = "";
$tipe_pesan = "";

// --- LOGIKA PENGAJUAN PINJAMAN BARU ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nominal = $_POST['nominal'];
    $tenor   = $_POST['tenor'];
    $alasan  = $_POST['alasan'];

    if (empty($nominal) || empty($tenor) || empty($alasan)) {
        $pesan = "Semua kolom wajib diisi!";
        $tipe_pesan = "error";
    } else {
        try {
            // 1. Insert ke tabel pinjaman
            $q1 = "INSERT INTO pinjaman (id_nasabah, nominal_pinjaman, tenor, alasan_pengajuan, tgl_pengajuan) 
                   VALUES (?, ?, ?, ?, NOW())";
            $pdo->prepare($q1)->execute([$id_nasabah, $nominal, $tenor, $alasan]);
            
            $id_baru = $pdo->lastInsertId();

            // 2. Insert status awal 'MENUNGGU'
            $q2 = "INSERT INTO status_pinjaman (id_pinjaman, tgl_status, status) 
                   VALUES (?, NOW(), 'MENUNGGU')";
            $pdo->prepare($q2)->execute([$id_baru]);

            $pesan = "Pengajuan berhasil dikirim! Silakan tunggu persetujuan Admin.";
            $tipe_pesan = "success";
            
            // Refresh halaman agar data baru muncul
            echo "<meta http-equiv='refresh' content='1'>";

        } catch (Exception $e) {
            $pesan = "Gagal: " . $e->getMessage();
            $tipe_pesan = "error";
        }
    }
}

// --- AMBIL RIWAYAT PINJAMAN ---
$query = "SELECT p.*, s.status 
          FROM pinjaman p
          JOIN status_pinjaman s ON p.id_pinjaman = s.id_pinjaman
          WHERE p.id_nasabah = ?
          ORDER BY p.tgl_pengajuan DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_nasabah]);
$riwayat = $stmt->fetchAll();

$min_baris = 10;
$sisa_baris = $min_baris - count($riwayat);
if ($sisa_baris < 0) $sisa_baris = 0;
?>

<?php if(!empty($pesan)): ?>
    <div class="msg-box <?php echo $tipe_pesan; ?>"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="dashboard-content">
    
    <div class="dashboard-welcome">
        <h2>💸 Ajukan dan pantau status pinjaman anda</h2>
    </div>

    <div class="widget-box">
        <div class="widget-header">
            <span>Riwayat Pengajuan Pinjaman</span>
            <button class="btn-admin-action" style="background: var(--success-color);" onclick="openModalPinjaman()">
                <span>+</span> Ajukan Pinjaman Baru
            </button>
        </div>
        <div class="widget-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="50" style="text-align:center;">ID</th>
                            <th style="text-align:center;">Nominal</th>
                            <th style="text-align:center;">Alasan</th>
                            <th style="text-align:center;">Tenor</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($riwayat as $row): ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $row['id_pinjaman']; ?></td>
                            <td style="text-align:center; font-weight:bold;">
                                Rp <?php echo formatRupiah($row['nominal_pinjaman']); ?>
                            </td>
                            <td style="text-align:left; font-size:13px;">
                                <?php echo htmlspecialchars($row['alasan_pengajuan']); ?>
                            </td>
                            <td style="text-align:center;"><?php echo $row['tenor']; ?> Bulan</td>
                            <td style="text-align:center;"><?php echo date('d-m-Y', strtotime($row['tgl_pengajuan'])); ?></td>
                            <td style="text-align:center;">
                                <?php 
                                    $s = $row['status'];
                                    $cls = 'badge-info';
                                    if($s == 'DISETUJUI') $cls = 'badge-success';
                                    if($s == 'DITOLAK') $cls = 'badge-danger';
                                    if($s == 'LUNAS') $cls = 'badge-active';
                                    if($s == 'MENUNGGU') $cls = 'badge-warning';
                                ?>
                                <span class="status-badge <?php echo $cls; ?>"><?php echo $s; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($riwayat)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:30px; color:#888;">
                                    Anda belum memiliki riwayat pinjaman.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="loanModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 450px;">
        <div class="modal-header">
            <span style="font-weight:bold; font-size:16px;">Form Pengajuan Pinjaman</span>
            <span class="close-btn" onclick="closeModal('loanModal')">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <div class="admin-form-row" style="display:block; margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Nominal Pinjaman (Rp)</label>
                    <input type="number" name="nominal" placeholder="Contoh: 2000000" required min="10000" style="width:100%;">
                </div>

                <div class="admin-form-row" style="display:block; margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Pilih Tenor (Bulan)</label>
                    <select name="tenor" required style="width:100%;">
                        <option value="" disabled selected>-- Pilih Tenor --</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                        <option value="24">24 Bulan</option>
                    </select>
                </div>

                <div class="admin-form-row" style="display:block; margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Alasan Pengajuan</label>
                    <textarea name="alasan" rows="3" placeholder="Contoh: Untuk renovasi rumah..." required style="width:100%; padding:5px; border:1px solid #7f9db9; border-radius:2px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-admin-action" style="width: 100%; background: #28a745;">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fungsi khusus untuk membuka modal ini
    function openModalPinjaman() { 
        document.getElementById('loanModal').style.display = 'block'; 
    }
    // Pastikan fungsi closeModal umum ada di script.js, atau gunakan yang ini:
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>

<?php require_once '../includes/footer.php'; ?>