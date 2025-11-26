<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin();

$pesan = "";
$tipe_pesan = "";

// --- LOGIKA UPDATE DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_nasabah = $_POST['id_nasabah'];
    $nama       = $_POST['nama_lengkap'];
    $jabatan    = $_POST['id_jabatan'];
    $username   = $_POST['username'];
    $status     = $_POST['status'];
    $password   = $_POST['password']; 

    try {
        if (!empty($password)) {
            $sql = "UPDATE nasabah SET id_jabatan=?, nama_lengkap=?, username=?, status=?, hashed_password=? WHERE id_nasabah=?";
            $params = [$jabatan, $nama, $username, $status, $password, $id_nasabah];
        } else {
            $sql = "UPDATE nasabah SET id_jabatan=?, nama_lengkap=?, username=?, status=? WHERE id_nasabah=?";
            $params = [$jabatan, $nama, $username, $status, $id_nasabah];
        }
        $pdo->prepare($sql)->execute($params);
        
        $pesan = "Data nasabah berhasil diperbarui!";
        $tipe_pesan = "success";
    } catch (Exception $e) {
        $pesan = "Gagal Update: " . $e->getMessage();
        $tipe_pesan = "error";
    }
}

// --- AMBIL DATA ---
$data_nasabah = $pdo->query("SELECT nasabah.*, jabatan.nama_jabatan FROM nasabah LEFT JOIN jabatan ON nasabah.id_jabatan = jabatan.id_jabatan ORDER BY nasabah.id_nasabah ASC")->fetchAll();
$list_jabatan = $pdo->query("SELECT * FROM jabatan")->fetchAll();
?>

<?php if (!empty($pesan)): ?>
    <div style="padding:15px; margin-bottom:20px; border-radius:5px; text-align:center; font-weight:bold; 
         background-color: <?= $tipe_pesan == 'success' ? '#d4edda' : '#f8d7da' ?>; 
         color: <?= $tipe_pesan == 'success' ? '#155724' : '#721c24' ?>;">
        <?= $pesan; ?>
    </div>
<?php endif; ?>

<div class="window-panel">
    <div class="window-header-strip"><div class="window-icon"></div></div>
    
    <div style="padding: 15px; text-align:right;">
        <button onclick="window.print()" class="btn-admin-action" style="background:#17a2b8;">🖨️ Cetak Laporan</button>
    </div>

    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jabatan</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_nasabah as $row): ?>
                <tr>
                    <td><?= $row['id_nasabah']; ?></td>
                    <td><?= htmlspecialchars($row['nama_jabatan']); ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td>
                        <span style="padding:3px 8px; border-radius:4px; font-size:11px; color:white; background-color: <?= $row['status']=='AKTIF'?'#28a745':'#dc3545' ?>">
                            <?= $row['status']; ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn-edit"
                            data-id="<?= $row['id_nasabah']; ?>"
                            data-nama="<?= htmlspecialchars($row['nama_lengkap']); ?>"
                            data-user="<?= htmlspecialchars($row['username']); ?>"
                            data-jabatan="<?= $row['id_jabatan']; ?>"
                            data-status="<?= $row['status']; ?>"
                            onclick="openEditNasabah(this)">
                            Edit
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        
        <div class="modal-header">
            <span>✏️ Edit Data Nasabah</span>
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
        </div>
        
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_nasabah" id="modal_id">

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="modal_nama" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="modal_user" required>
                </div>

                <div class="form-group">
                    <label>Jabatan</label>
                    <select name="id_jabatan" id="modal_jabatan">
                        <?php foreach($list_jabatan as $jab): ?>
                            <option value="<?= $jab['id_jabatan']; ?>"><?= $jab['nama_jabatan']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Akun</label>
                    <select name="status" id="modal_status">
                        <option value="AKTIF">AKTIF</option>
                        <option value="SUSPEND">SUSPEND</option>
                    </select>
                </div>

                <div class="form-group" style="border-top:1px dashed #ccc; padding-top:10px; margin-top:15px;">
                    <label>Password Baru</label>
                    <input type="password" name="password" id="modal_pass" placeholder="Ketik password baru...">
                    <span class="form-note">⚠️ Biarkan kosong jika tidak ingin mengganti password.</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div> 
</div>

<?php require_once '../includes/footer.php';