<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin(); 

$pesan = "";
$tipe_pesan = "";

// --- LOGIKA PROSES (TERIMA / TOLAK) ---
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_pinjaman = $_GET['id'];
    $aksi = $_GET['aksi']; 

    try {
        $status_baru = ($aksi == 'terima') ? 'DISETUJUI' : 'DITOLAK';

        $query = "UPDATE status_pinjaman SET status = ?, tgl_status = NOW() WHERE id_pinjaman = ?";
        $pdo->prepare($query)->execute([$status_baru, $id_pinjaman]);

        $pesan = "Status pinjaman berhasil diubah menjadi " . $status_baru;
        $tipe_pesan = "success";
        
    } catch (Exception $e) {
        $pesan = "Gagal mengubah status: " . $e->getMessage();
        $tipe_pesan = "error";
    }
}

// --- AMBIL DATA PINJAMAN ---
$query = "SELECT p.*, n.nama_lengkap, s.status 
          FROM pinjaman p
          JOIN nasabah n ON p.id_nasabah = n.id_nasabah
          JOIN status_pinjaman s ON p.id_pinjaman = s.id_pinjaman
          ORDER BY p.tgl_pengajuan DESC";

$data_pinjaman = $pdo->query($query)->fetchAll();

$min_baris = 10;
$sisa_baris = $min_baris - count($data_pinjaman);
if ($sisa_baris < 0) $sisa_baris = 0;
?>

<?php if (!empty($pesan)): ?>
    <div class="msg-box <?php echo $tipe_pesan; ?>"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="dashboard-content">
    
    <div class="dashboard-welcome">
        <h2>📋 Persetujuan Pinjaman</h2>
        <span>Kelola pengajuan pinjaman nasabah</span>
    </div>

    <div class="widget-box">
        <div class="widget-header">
            <span>Daftar Pengajuan Pinjaman</span>
        </div>
        <div class="widget-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nasabah</th>
                            <th>Nominal (Rp)</th>
                            <th>Tenor</th>
                            <th>Alasan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_pinjaman as $row): ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $row['id_pinjaman']; ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-small"><?= strtoupper(substr($row['nama_lengkap'],0,2)) ?></div>
                                    <div class="font-bold"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                                </div>
                            </td>
                            <td style="text-align:right; font-weight:bold; color:#0f172a;">
                                <?php echo formatRupiah($row['nominal_pinjaman']); ?>
                            </td>
                            <td style="text-align:center;"><?php echo $row['tenor']; ?> Bulan</td>
                            <td style="font-size:12px; max-width:150px; color:#64748b;">
                                <?php echo htmlspecialchars($row['alasan_pengajuan']); ?>
                            </td>
                            <td style="text-align:center;"><?php echo date('d-m-Y', strtotime($row['tgl_pengajuan'])); ?></td>
                            
                            <td style="text-align:center;">
                                <?php 
                                    $st = $row['status'];
                                    $cls = 'badge-info';
                                    if($st == 'DISETUJUI') $cls = 'badge-success';
                                    if($st == 'DITOLAK') $cls = 'badge-danger';
                                    if($st == 'LUNAS') $cls = 'badge-active';
                                ?>
                                <span class="status-badge <?php echo $cls; ?>"><?php echo $st; ?></span>
                            </td>

                            <td style="text-align:center;">
                                <?php if($row['status'] == 'MENUNGGU'): ?>
                                    
                                    <a href="?aksi=terima&id=<?php echo $row['id_pinjaman']; ?>" 
                                       class="btn-icon btn-approve" 
                                       title="Setujui"
                                       onclick="return confirm('Setujui pinjaman nasabah ini?');">
                                       &#10003;
                                    </a>

                                    <a href="?aksi=tolak&id=<?php echo $row['id_pinjaman']; ?>" 
                                       class="btn-icon btn-reject" 
                                       title="Tolak"
                                       onclick="return confirm('Tolak pengajuan ini?');">
                                       &#10005;
                                    </a>

                                <?php else: ?>
                                    <span style="color:#cbd5e1; font-size:12px; font-weight:600;">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php for($i=0; $i < $sisa_baris; $i++): ?>
                        <tr>
                            <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>