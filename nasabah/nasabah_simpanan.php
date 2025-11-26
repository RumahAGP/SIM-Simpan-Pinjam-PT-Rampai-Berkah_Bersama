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

<div class="window-panel">
    <div class="window-header-strip"><div class="window-icon"></div></div>
    
    <div class="form-wrapper" style="padding-top:20px;">
        
        <div style="background: linear-gradient(135deg, #28a745, #198754); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8;">Total Saldo Simpanan</div>
                <div style="font-size: 36px; font-weight: bold; margin-top: 5px;">
                    Rp <?php echo formatRupiah($total_saldo); ?>
                </div>
            </div>
            <div style="font-size: 50px;">💰</div>
        </div>

        <h4 style="margin-bottom:15px; border-left:4px solid #4ca1af; padding-left:10px;">Riwayat Transaksi Masuk</h4>

        <div class="simpanan-table-container">
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
                        <td style="text-align:right; font-weight:bold; color: #198754;">
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

                    <?php for($i=0; $i<$sisa_baris; $i++): ?>
                        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; 