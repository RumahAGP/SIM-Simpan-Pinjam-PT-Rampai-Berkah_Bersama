<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginNasabah(); 

// 1. Set Timezone & ID
date_default_timezone_set('Asia/Jakarta');
$id_nasabah = (int) $_SESSION['user_id'];

try {
    // --- A. DATA STATISTIK (KARTU ATAS) ---
    $total_simpanan = $pdo->query("SELECT COALESCE(SUM(nominal_simpanan),0) FROM simpanan WHERE id_nasabah = $id_nasabah")->fetchColumn();

    // Pinjaman Aktif (HANYA YANG DISETUJUI)
    $total_pinjaman_aktif = $pdo->query("
        SELECT SUM(p.nominal_pinjaman) FROM pinjaman p 
        JOIN status_pinjaman sp ON p.id_pinjaman = sp.id_pinjaman
        WHERE sp.status = 'DISETUJUI' AND p.id_nasabah = $id_nasabah
    ")->fetchColumn() ?? 0;

    // Total yang sudah dibayar
    $total_sudah_bayar = $pdo->query("
        SELECT COALESCE(SUM(a.nominal_angsuran),0) FROM angsuran a 
        JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman
        WHERE p.id_nasabah = $id_nasabah
    ")->fetchColumn();

    // Status Pengajuan Terakhir
    $pengajuan_terakhir = $pdo->query("
        SELECT p.id_pinjaman, p.nominal_pinjaman, p.tenor, sp.status 
        FROM pinjaman p
        LEFT JOIN status_pinjaman sp ON p.id_pinjaman = sp.id_pinjaman
        WHERE p.id_nasabah = $id_nasabah
        ORDER BY p.tgl_pengajuan DESC LIMIT 1
    ")->fetch();


    // --- B. LOGIKA PERSENTASE & TAGIHAN ---
    $persen_lunas = 0;
    $next_bill = null;
    
    if ($total_pinjaman_aktif > 0) {
        $bayar_total = $pdo->query("SELECT COALESCE(SUM(nominal_angsuran),0) FROM angsuran a JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman JOIN status_pinjaman sp ON p.id_pinjaman = sp.id_pinjaman WHERE p.id_nasabah = $id_nasabah AND sp.status = 'DISETUJUI'")->fetchColumn() ?? 0;
        $persen_lunas = min(100, round(($bayar_total / $total_pinjaman_aktif) * 100));
    }

    if ($pengajuan_terakhir && $pengajuan_terakhir['status'] == 'DISETUJUI') {
        $id_p       = $pengajuan_terakhir['id_pinjaman'];
        $hutang_p   = $pengajuan_terakhir['nominal_pinjaman'];
        $tenor_p    = $pengajuan_terakhir['tenor'];
        $bayar_p    = $pdo->query("SELECT COUNT(*) FROM angsuran WHERE id_pinjaman = $id_p")->fetchColumn();
        
        if ($bayar_p < $tenor_p) {
            $cicilan = round($hutang_p / $tenor_p);
            $next_bill = ['ke' => $bayar_p + 1, 'nominal' => $cicilan, 'sisa' => $tenor_p - $bayar_p];
        }
    }


    // --- C. DATA GRAFIK (TREN 6 BULAN - DATA LENGKAP) ---
    $chart_labels = []; 
    $d_simpan = []; 
    $d_pinjam = []; // Data Pinjaman Dimunculkan Lagi
    $d_bayar  = []; 
    
    for ($i = 5; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime("-$i months"));
        $chart_labels[] = date('M Y', strtotime($ym . '-01'));
        
        // Pastikan menggunakan (int) agar tidak null dan grafik tidak error
        $d_simpan[] = (int) $pdo->query("SELECT COALESCE(SUM(nominal_simpanan),0) FROM simpanan WHERE id_nasabah = $id_nasabah AND tgl_uang_masuk LIKE '$ym%'")->fetchColumn();
        
        // Pinjaman (Hanya yang DISETUJUI agar akurat)
        $d_pinjam[] = (int) $pdo->query("
            SELECT COALESCE(SUM(p.nominal_pinjaman),0) FROM pinjaman p
            JOIN status_pinjaman sp ON p.id_pinjaman = sp.id_pinjaman
            WHERE p.id_nasabah = $id_nasabah 
            AND p.tgl_pengajuan LIKE '$ym%'
            AND sp.status = 'DISETUJUI' 
        ")->fetchColumn();

        $d_bayar[]  = (int) $pdo->query("
            SELECT COALESCE(SUM(a.nominal_angsuran),0) FROM angsuran a 
            JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman 
            WHERE p.id_nasabah = $id_nasabah AND a.tgl_pembayaran LIKE '$ym%'
        ")->fetchColumn();
    }


    // --- D. LOG AKTIVITAS ---
    $sql_log = "SELECT * FROM (
        SELECT s.tgl_uang_masuk AS t, 'Simpanan' AS j, s.nominal_simpanan AS n FROM simpanan s WHERE s.id_nasabah = $id_nasabah
        UNION ALL
        SELECT p.tgl_pengajuan AS t, 'Pinjaman' AS j, p.nominal_pinjaman AS n FROM pinjaman p WHERE p.id_nasabah = $id_nasabah
        UNION ALL
        SELECT a.tgl_pembayaran AS t, 'Pembayaran' AS j, a.nominal_angsuran AS n FROM angsuran a JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman WHERE p.id_nasabah = $id_nasabah
    ) AS log_all ORDER BY t DESC LIMIT 5";
    $logs = $pdo->query($sql_log)->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="dashboard-content">
    
    <div class="dashboard-welcome">
        <h2>👋 Halo, <?= htmlspecialchars($_SESSION['user_login']) ?></h2>
        <span>Selamat datang kembali di dashboard sistem informasi simpan pinjam</span>
    </div>

    <?php if($next_bill): ?>
    <div class="bill-alert">
        <div>
            <strong>🔔 Tagihan Bulan Ini: Angsuran Ke-<?= $next_bill['ke'] ?></strong>
            <span>Nominal: <b>Rp <?= formatRupiah($next_bill['nominal']) ?></b> (Sisa <?= $next_bill['sisa'] ?>x lagi)</span>
        </div>
        <a href="nasabah_pembayaran.php" class="btn-admin-action" style="text-decoration:none; background:#ffc107; color:#333;">Bayar</a>
    </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <div class="info-card card-simpanan">
            <h3>Total Simpanan</h3>
            <span class="data-value">Rp <?= formatRupiah($total_simpanan) ?></span>
        </div>

        <div class="info-card card-pinjaman">
            <h3>Pinjaman Aktif (Disetujui)</h3>
            <span class="data-value">Rp <?= formatRupiah($total_pinjaman_aktif) ?></span>
            
            <?php if($total_pinjaman_aktif > 0): ?>
            <div class="loan-progress-container">
                <div class="progress-label">
                    <span>Terbayar</span>
                    <span><?= $persen_lunas ?>%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: <?= $persen_lunas ?>%;"></div>
                </div>
            </div>
            <?php else: ?>
                <p style="font-size:11px; color:#888; margin-top:5px;">Tidak ada hutang aktif</p>
            <?php endif; ?>
        </div>

        <div class="info-card card-pembayaran">
            <h3>Sudah Dibayar</h3>
            <span class="data-value">Rp <?= formatRupiah($total_sudah_bayar) ?></span>
        </div>

        <div class="info-card card-menunggu">
            <h3>Status Terakhir</h3>
            <span class="data-value" style="font-size:18px;">
                <?= $pengajuan_terakhir['status'] ?? '-'; ?>
            </span>
            <p style="font-size:11px; color:#888; margin-top:5px;">
                <?= ($pengajuan_terakhir) ? formatRupiah($pengajuan_terakhir['nominal_pinjaman']) : '' ?>
            </p>
        </div>
    </div>

    <div class="row-2-col">
        <div class="widget-box">
            <div class="widget-header">
                <span>📈 Grafik Keuangan Saya (6 Bulan)</span>
            </div>
            <div class="widget-body">
                <div class="chart-container-fixed">
                    <canvas id="chart-keuangan"></canvas>
                </div>
            </div>
        </div>
        
        <div class="widget-box">
            <div class="widget-header"><span>🔔 Aktivitas Terakhir</span></div>
            <div class="widget-body activity-widget-body">
                <table class="table-widget">
                    <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach($logs as $l): 
                            $badgeClass = $l['j']=='Simpanan'?'badge-success':($l['j']=='Pinjaman'?'badge-danger':'badge-info');
                        ?>
                        <tr>
                            <td><span class="status-badge <?= $badgeClass ?>"><?= $l['j'] ?></span></td>
                            <td class="font-bold"><?= formatRupiah($l['n']) ?></td>
                            <td class="text-muted"><?= date('d M H:i', strtotime($l['t'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted">Belum ada aktivitas.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Data Grafik dari PHP
    const labels   = <?= json_encode($chart_labels); ?>;
    const dSimpan  = <?= json_encode($d_simpan); ?>;
    const dPinjam  = <?= json_encode($d_pinjam); ?>; // Data Pinjaman (Ada isinya sekarang)
    const dBayar   = <?= json_encode($d_bayar); ?>;

    // Inisialisasi Grafik
    if(typeof initDashboardChart === 'function') {
        // Kirim ke-3 data: Simpan, Pinjam, Bayar
        initDashboardChart(labels, dSimpan, dPinjam, dBayar);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>