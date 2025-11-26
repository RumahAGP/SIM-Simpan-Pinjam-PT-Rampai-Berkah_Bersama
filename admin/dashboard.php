<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin(); 

// Set Timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// --- A. DATA STATISTIK (CARD) ---
try {
    // Statistik Dasar
    $total_nasabah = $pdo->query("SELECT COUNT(*) FROM nasabah WHERE status = 'AKTIF'")->fetchColumn() ?? 0;
    $total_simpanan = $pdo->query("SELECT COALESCE(SUM(nominal_simpanan),0) FROM simpanan")->fetchColumn() ?? 0;
    $total_pembayaran = $pdo->query("SELECT COALESCE(SUM(nominal_angsuran),0) FROM angsuran")->fetchColumn() ?? 0;

    $total_pinjaman = $pdo->query("SELECT SUM(p.nominal_pinjaman) FROM pinjaman p JOIN status_pinjaman sp ON p.id_pinjaman=sp.id_pinjaman WHERE sp.status IN ('DISETUJUI','MENUNGGU')")->fetchColumn() ?? 0;
    $total_menunggu = $pdo->query("SELECT COUNT(*) FROM status_pinjaman WHERE status='MENUNGGU'")->fetchColumn() ?? 0;

    // --- B. DATA GRAFIK TREN (Line Chart) ---
    $chart_labels = []; $d_simpan = []; $d_pinjam = []; $d_bayar = [];
    for ($i = 5; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime("-$i months"));
        $chart_labels[] = date('M Y', strtotime($ym . '-01'));
        
        $d_simpan[] = $pdo->query("SELECT COALESCE(SUM(nominal_simpanan),0) FROM simpanan WHERE tgl_uang_masuk LIKE '$ym%'")->fetchColumn();
        $d_pinjam[] = $pdo->query("SELECT COALESCE(SUM(nominal_pinjaman),0) FROM pinjaman WHERE tgl_pengajuan LIKE '$ym%'")->fetchColumn();
        $d_bayar[]  = $pdo->query("SELECT COALESCE(SUM(nominal_angsuran),0) FROM angsuran WHERE tgl_pembayaran LIKE '$ym%'")->fetchColumn();
    }

    // --- C. DATA GRAFIK STATUS (Pie Chart) ---
    $pie_data = $pdo->query("SELECT status, COUNT(*) as jumlah FROM status_pinjaman GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
    // Urutan: Disetujui(Hijau), Menunggu(Kuning), Ditolak(Merah), Lunas(Biru)
    $pie_values = [
        $pie_data['DISETUJUI'] ?? 0,
        $pie_data['MENUNGGU'] ?? 0,
        $pie_data['DITOLAK'] ?? 0,
        $pie_data['LUNAS'] ?? 0
    ];
    $pie_labels = ['Disetujui', 'Menunggu', 'Ditolak', 'Lunas'];

    // --- D. LOG AKTIVITAS ---
    $sql_log = "SELECT * FROM (
        SELECT tgl_uang_masuk AS t, 'Simpanan' AS j, nominal_simpanan AS n FROM simpanan
        UNION ALL
        SELECT tgl_pengajuan, 'Pinjaman', nominal_pinjaman FROM pinjaman
        UNION ALL
        SELECT tgl_pembayaran, 'Pembayaran', nominal_angsuran FROM angsuran
    ) AS a ORDER BY t DESC LIMIT 5";
    $logs = $pdo->query($sql_log)->fetchAll();

    // --- E. MEMBER BARU ---
    $new_members = $pdo->query("SELECT nama_lengkap, username FROM nasabah ORDER BY id_nasabah DESC LIMIT 5")->fetchAll();

} catch (Exception $e) {
    echo "Error Data: " . $e->getMessage();
}
?>

<div class="window-panel"><div style="padding:20px;">

    <div class="dashboard-grid">
        <div class="info-card card-nasabah"><h3>👥 Nasabah</h3><span class="data-value"><?= $total_nasabah ?></span><p>Aktif</p></div>
        <div class="info-card card-simpanan"><h3>📥 Simpanan</h3><span class="data-value">Rp <?= number_format($total_simpanan/1000000, 1, ',', '.') ?> Jt</span><p>Total Aset</p></div>
        <div class="info-card card-pinjaman"><h3>📤 Pinjaman</h3><span class="data-value">Rp <?= number_format($total_pinjaman/1000000, 1, ',', '.') ?> Jt</span><p>Sedang Jalan</p></div>
        <div class="info-card card-pembayaran"><h3>🔄 Pembayaran</h3><span class="data-value">Rp <?= number_format($total_pembayaran/1000000, 1, ',', '.') ?> Jt</span><p>Total Masuk</p></div>
        <div class="info-card card-menunggu"><h3>⏳ Pending</h3><span class="data-value"><?= $total_menunggu ?></span><p>Perlu Cek</p></div>
    </div>

    <div class="row-2-col">
        <div class="widget-box">
            <div class="widget-header"><span>📈 Tren Keuangan (6 Bulan)</span></div>
            <div class="widget-body">
                <div class="chart-container-fixed"><canvas id="chart-keuangan"></canvas></div>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-header"><span>📊 Status Pengajuan</span></div>
            <div class="widget-body">
                <div class="chart-container-fixed" style="height:250px; margin-top:25px;"><canvas id="chart-status"></canvas></div>
                <div style="text-align:center; font-size:12px; color:#666; margin-top:10px;">Distribusi Status</div>
            </div>
        </div>
    </div>

    <div class="row-2-col">
        <div class="widget-box">
            <div class="widget-header"><span>🔔 Aktivitas Terakhir</span></div>
            <div class="widget-body">
                <table class="table-widget">
                    <thead><tr><th>Jenis</th><th>Nominal</th><th>Waktu</th></tr></thead>
                    <tbody>
                    <?php foreach($logs as $l): 
                        $bg = $l['j']=='Simpanan'?'#e8f5e9':($l['j']=='Pinjaman'?'#ffebee':'#e3f2fd');
                        $cl = $l['j']=='Simpanan'?'green':($l['j']=='Pinjaman'?'red':'blue');
                    ?>
                    <tr>
                        <td><span style="background:<?=$bg?>; color:<?=$cl?>; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:bold;"><?= $l['j'] ?></span></td>
                        <td style="font-weight:bold;"><?= formatRupiah($l['n']) ?></td>
                        <td style="color:#888;"><?= date('d M H:i', strtotime($l['t'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-header"><span>👤 Nasabah Terbaru</span><a href="data_nasabah.php" style="font-size:12px; text-decoration:none;">Kelola</a></div>
            <div class="widget-body">
                <table class="table-widget">
                    <tbody>
                    <?php foreach($new_members as $m): ?>
                    <tr>
                        <td style="display:flex; align-items:center; border:none;">
                            <div class="user-avatar"><?= strtoupper(substr($m['nama_lengkap'],0,2)) ?></div>
                            <div>
                                <div style="font-weight:bold;"><?= htmlspecialchars($m['nama_lengkap']) ?></div>
                                <div style="font-size:11px; color:#999;">@<?= htmlspecialchars($m['username']) ?></div>
                            </div>
                        </td>
                        <td style="text-align:right; border:none;"><span style="background:#d1e7dd; color:#0f5132; padding:2px 6px; border-radius:4px; font-size:10px;">Baru</span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div></div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inisialisasi Grafik Tren
    initDashboardChart(
        <?= json_encode($chart_labels) ?>, 
        <?= json_encode($d_simpan) ?>, 
        <?= json_encode($d_pinjam) ?>, 
        <?= json_encode($d_bayar) ?>
    );
    
    // Inisialisasi Grafik Pie (Cek dulu fungsinya ada atau tidak)
    if(typeof initPieChart === 'function') {
        initPieChart(
            <?= json_encode($pie_labels) ?>, 
            <?= json_encode($pie_values) ?>
        );
    }
});
</script>

<?php require_once '../includes/footer.php';