<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin(); 

$pesan = "";
$tipe_pesan = "";
$nominal = "";

// --- PROSES SIMPAN DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_nasabah = $_POST['id_nasabah'] ?? '';
    $nominal    = $_POST['nominal'] ?? '';
    $tanggal    = $_POST['tanggal'] ?? '';

    if (empty($id_nasabah) || empty($nominal) || empty($tanggal)) {
        $pesan = "Semua kolom wajib diisi!";
        $tipe_pesan = "error";
    } else {
        try {
            $query = "INSERT INTO simpanan (id_nasabah, nominal_simpanan, tgl_uang_masuk) VALUES (?, ?, ?)";
            $pdo->prepare($query)->execute([$id_nasabah, $nominal, $tanggal]);

            $pesan = "Data simpanan berhasil ditambahkan!";
            $tipe_pesan = "success";
            $nominal = ""; 

        } catch (Exception $e) {
            $pesan = "Gagal menyimpan: " . $e->getMessage();
            $tipe_pesan = "error";
        }
    }
}

// --- AMBIL DATA NASABAH (Bisa banyak) ---
$list_nasabah = $pdo->query("SELECT id_nasabah, nama_lengkap, username FROM nasabah WHERE status = 'AKTIF' ORDER BY nama_lengkap ASC")->fetchAll();
?>

<?php if (!empty($pesan)): ?>
    <div class="msg-box <?php echo $tipe_pesan; ?>"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="window-panel">
    <div class="window-header-strip"><div class="window-icon"></div></div>
    <div class="form-wrapper">

        <form method="POST" action="">
            <div class="admin-form-row">
                <label>Pilih Nasabah</label>
                <input type="text" id="inputNasabah" class="input-readonly" placeholder="Klik untuk cari nasabah..."
                       readonly onclick="openLookupNasabah()" style="cursor: pointer; background-color: #fff;">
                <input type="hidden" name="id_nasabah" id="idNasabahHidden" required>
            </div>

            <div class="admin-form-row">
                <label>Nominal (Rp)</label>
                <input type="number" name="nominal" placeholder="Contoh: 50000" 
                       value="<?php echo htmlspecialchars($nominal); ?>" required>
            </div>

            <div class="admin-form-row">
                <label>Tgl Uang Masuk</label>
                <input type="date" name="tanggal" style="width: 160px; flex:none;" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="admin-btn-container">
                <button type="submit" class="btn-admin-action">Simpan</button>
                <a href="simpanan.php" class="btn-admin-action" style="background:#d9534f; text-decoration:none; text-align:center; line-height:normal;">Batal</a>
            </div>
        </form>

    </div>
</div>

<div id="lookupModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 600px;">
        <div class="modal-header">
            <span>Pilih Nasabah (Klik Data)</span>
            <span class="close-btn" onclick="closeModal('lookupModal')">&#10005;</span>
        </div>
        <div class="modal-body">
            <input type="text" id="searchNasabah" placeholder="🔍 Ketik nama nasabah..." 
                   onkeyup="filterTable('searchNasabah', 'tableNasabah')" 
                   style="width:100%; margin-bottom:10px; padding:10px; border:1px solid #ccc; border-radius:4px;">
            
            <div class="table-scroll-container">
                <table class="lookup-table" id="tableNasabah">
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list_nasabah as $n): ?>
                        <tr onclick="selectRowNasabah('<?php echo $n['id_nasabah']; ?>', '<?php echo htmlspecialchars($n['nama_lengkap'], ENT_QUOTES); ?>')">
                            <td style="text-align:center;"><?php echo $n['id_nasabah']; ?></td>
                            <td style="font-weight:bold; color:#007bff;"><?php echo htmlspecialchars($n['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($n['username']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($list_nasabah)): ?>
                            <tr><td colspan="3" style="text-align:center; padding:20px;">Tidak ada data nasabah aktif.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px; font-size:12px; color:#666; text-align:right;">
                Total Data: <?php echo count($list_nasabah); ?>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center;">
            <button type="button" class="btn-admin-action" style="background:#888;" onclick="closeModal('lookupModal')">Batal</button>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; 