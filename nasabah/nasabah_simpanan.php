<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginNasabah(); 

$id_nasabah = $_SESSION['user_id']; 

// --- QUERY TOTAL ---
$total_saldo = $pdo->query("SELECT SUM(nominal_simpanan) as total FROM simpanan WHERE id_nasabah = $id_nasabah")->fetchColumn() ?? 0;

// --- QUERY RIWAYAT ---
$riwayat = $pdo->query("SELECT * FROM simpanan WHERE id_nasabah = $id_nasabah ORDER BY tgl_uang_masuk DESC")->fetchAll();

$min_baris = 10;
$sisa_baris = $min_baris - count($riwayat);
if ($sisa_baris < 0) $sisa_baris = 0;
?>

<div class="dashboard-content">
    
    <div class="dashboard-welcome">
        <h2>💰 Riwayat setoran simpanan anda</h2>
    </div>

    <div class="row-2-col">
        <div class="info-card card-simpanan">
            <h3>Total Saldo Simpanan</h3>
            <span class="data-value">Rp <?php echo formatRupiah($total_saldo); ?></span>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-header">
            <span>Riwayat Transaksi Masuk</span>
        </div>
        <div class="widget-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="50" style="text-align:center;">No</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:right;">Nominal</th>
                            <th style="text-align:center;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($riwayat as $row): 
                        ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $no++; ?></td>
                            <td style="text-align:center;"><?php echo date('d-m-Y', strtotime($row['tgl_uang_masuk'])); ?></td>
                            <td style="text-align:right; font-weight:bold; color: var(--success-color);">
                                + Rp <?php echo formatRupiah($row['nominal_simpanan']); ?>
                            </td>
                            <td style="text-align:center;">Setoran Simpanan</td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($riwayat)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:20px; color:#888;">
                                    Belum ada data simpanan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>