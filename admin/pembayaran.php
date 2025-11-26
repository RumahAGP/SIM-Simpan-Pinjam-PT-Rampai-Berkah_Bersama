<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin(); 

$pesan = "";
$tipe_pesan = "";

$data_pinjaman = null;
$riwayat_angsuran = [];
$tagihan_per_bulan = 0;
$angsuran_ke_next = 1;
$id_cari = isset($_GET['id_pinjaman']) ? $_GET['id_pinjaman'] : '';

// --- LOGIKA PENCARIAN ---
if (!empty($id_cari)) {
    $sql = "SELECT p.*, n.nama_lengkap FROM pinjaman p JOIN nasabah n ON p.id_nasabah = n.id_nasabah WHERE p.id_pinjaman = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cari]);
    $data_pinjaman = $stmt->fetch();

    if ($data_pinjaman) {
        if ($data_pinjaman['tenor'] > 0) {
            $tagihan_per_bulan = $data_pinjaman['nominal_pinjaman'] / $data_pinjaman['tenor'];
        }
        
        $riwayat = $pdo->prepare("SELECT * FROM angsuran WHERE id_pinjaman = ? ORDER BY angsuran_ke ASC");
        $riwayat->execute([$id_cari]);
        $riwayat_angsuran = $riwayat->fetchAll();
        $angsuran_ke_next = count($riwayat_angsuran) + 1;
    } else {
        $pesan = "ID Pinjaman tidak ditemukan!";
        $tipe_pesan = "error";
    }
}

// --- LOGIKA BAYAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pinjam  = $_POST['id_pinjaman_hide']; 
    $ke         = $_POST['angsuran_ke'];
    $nominal    = $_POST['nominal_bayar']; 
    $tgl        = $_POST['tanggal'];
    $metode     = 4; 

    if (empty($id_pinjam) || empty($nominal) || empty($tgl)) {
        $pesan = "Data tidak lengkap!";
        $tipe_pesan = "error";
    } else {
        try {
            $sql = "INSERT INTO angsuran (id_pinjaman, id_metode_pembayaran, angsuran_ke, nominal_angsuran, tgl_pembayaran, status) 
                    VALUES (?, ?, ?, ?, ?, 'LUNAS')";
            $pdo->prepare($sql)->execute([$id_pinjam, $metode, $ke, $nominal, $tgl]);
            
            header("Location: pembayaran.php?id_pinjaman=" . $id_pinjam . "&msg=sukses");
            exit;
        } catch (Exception $e) {
            $pesan = "Gagal bayar: " . $e->getMessage();
            $tipe_pesan = "error";
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] == 'sukses') {
    $pesan = "Pembayaran berhasil disimpan!";
    $tipe_pesan = "success";
}

// --- DATA UNTUK MODAL (Bisa banyak) ---
$query_list = "SELECT p.id_pinjaman, p.nominal_pinjaman, p.tenor, p.tgl_pengajuan, n.nama_lengkap 
               FROM pinjaman p
               JOIN nasabah n ON p.id_nasabah = n.id_nasabah
               JOIN status_pinjaman s ON p.id_pinjaman = s.id_pinjaman
               WHERE s.status = 'DISETUJUI'
               ORDER BY p.id_pinjaman DESC";
$list_pinjaman = $pdo->query($query_list)->fetchAll();

$min_baris = 15;
$sisa_baris = $min_baris - count($riwayat_angsuran);
if ($sisa_baris < 0) $sisa_baris = 0;
?>

<?php if (!empty($pesan)): ?>
    <div class="msg-box <?php echo $tipe_pesan; ?>"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="window-panel">
    <div class="window-header-strip"><div class="window-icon"></div></div>
    
    <div class="split-layout" style="padding: 20px;">
        <div class="left-panel">
            
            <form method="GET" action="pembayaran.php" id="searchForm">
                <div class="admin-form-row">
                    <label>ID Pinjaman</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" name="id_pinjaman" id="inputIdPinjaman" 
                               value="<?php echo htmlspecialchars($id_cari); ?>" 
                               class="input-readonly" readonly 
                               onclick="openLookupPinjaman()"
                               placeholder="Klik cari pinjaman..." style="cursor:pointer; background:#fff;">
                    </div>
                </div>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="id_pinjaman_hide" value="<?php echo $id_cari; ?>">
                <input type="hidden" name="nominal_bayar" value="<?php echo round($tagihan_per_bulan); ?>">
                
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #ccc;">

                <div class="admin-form-row">
                    <label>Nasabah</label>
                    <input type="text" class="input-readonly" 
                           value="<?php echo ($data_pinjaman) ? htmlspecialchars($data_pinjaman['nama_lengkap']) : ''; ?>" readonly>
                </div>
                
                <div class="admin-form-row">
                    <label>Nominal Pinjaman</label>
                    <input type="text" class="input-readonly"
                           value="<?php echo ($data_pinjaman) ? formatRupiah($data_pinjaman['nominal_pinjaman']) : ''; ?>" readonly>
                </div>

                <div class="admin-form-row">
                    <label>Tagihan / Bulan</label>
                    <input type="text" class="input-readonly" style="font-weight:bold;"
                           value="<?php echo ($data_pinjaman) ? formatRupiah($tagihan_per_bulan) : ''; ?>" readonly>
                </div>

                <div class="admin-form-row">
                    <label>Angsuran Ke-</label>
                    <input type="text" name="angsuran_ke" class="input-readonly" 
                           value="<?php echo ($data_pinjaman) ? $angsuran_ke_next : ''; ?>" readonly>
                </div>
                
                <div class="admin-form-row">
                    <label>Tanggal Pembayaran</label>
                    <input type="date" name="tanggal" 
                           value="<?php echo date('Y-m-d'); ?>" 
                           <?php echo (!$data_pinjaman) ? 'disabled' : ''; ?>>
                </div>
                
                <div class="admin-btn-container" style="padding-left:0; gap:10px;">
                    <button type="submit" class="btn-admin-action" style="background:#28a745; width:100px;"
                        <?php echo (!$data_pinjaman) ? 'disabled' : ''; ?>>
                        Bayar
                    </button>
                    <button type="button" class="btn-admin-action" style="background:#17a2b8; width:100px;" 
                        onclick="window.print();" 
                        <?php echo (!$data_pinjaman) ? 'disabled' : ''; ?>>
                        Cetak
                    </button>
                </div>
            </form>
        </div>

        <div class="right-panel">
             <div class="table-wrapper" style="padding: 10px;">
                <h4 style="margin-top:0; margin-bottom:10px; border-bottom:1px solid #ddd; padding-bottom:5px;">Riwayat Angsuran (Pinjaman ID: <?php echo $id_cari; ?>)</h4>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Ke</th><th>Nominal</th><th>Tanggal Bayar</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($riwayat_angsuran)): ?>
                            <?php foreach($riwayat_angsuran as $row): ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $row['id_angsuran']; ?></td>
                                <td style="text-align:center;"><?php echo $row['angsuran_ke']; ?></td>
                                <td style="text-align:right;"><?php echo formatRupiah($row['nominal_angsuran']); ?></td>
                                <td style="text-align:center;"><?php echo date('d-m-Y', strtotime($row['tgl_pembayaran'])); ?></td>
                                <td style="color:green; text-align:center; font-weight:bold;">LUNAS</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php for($i=0; $i < $sisa_baris; $i++): ?>
                        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="lookupModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 700px;">
        <div class="modal-header">
            <span>Pilih Pinjaman Aktif (Klik Data)</span>
            <span class="close-btn" onclick="closeModal('lookupModal')">&#10005;</span>
        </div>
        <div class="modal-body">
            <input type="text" id="searchPinjam" placeholder="🔍 Cari nama nasabah..." 
                   onkeyup="filterTable('searchPinjam', 'tablePinjaman')" 
                   style="width:100%; margin-bottom:10px; padding:10px; border:1px solid #ccc; border-radius:4px;">

            <div class="table-scroll-container">
                <table class="lookup-table" id="tablePinjaman">
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Nasabah</th>
                            <th>Nominal</th>
                            <th style="width:60px;">Tenor</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list_pinjaman as $lp): ?>
                            <tr onclick="selectRowPinjaman('<?php echo $lp['id_pinjaman']; ?>')" style="cursor:pointer;">
                                <td style="text-align:center;"><?php echo $lp['id_pinjaman']; ?></td>
                                <td style="font-weight:bold; color:#007bff;"><?php echo htmlspecialchars($lp['nama_lengkap']); ?></td>
                                <td><?php echo formatRupiah($lp['nominal_pinjaman']); ?></td>
                                <td style="text-align:center;"><?php echo $lp['tenor']; ?> Bln</td>
                                <td><?php echo date('d-m-Y', strtotime($lp['tgl_pengajuan'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($list_pinjaman)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:20px;">Tidak ada pinjaman yang DISETUJUI.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px; font-size:12px; color:#666; text-align:right;">
                Total Data: <?php echo count($list_pinjaman); ?>
            </div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button type="button" class="btn-admin-action" style="background:#888;" onclick="closeModal('lookupModal')">Batal</button>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; 