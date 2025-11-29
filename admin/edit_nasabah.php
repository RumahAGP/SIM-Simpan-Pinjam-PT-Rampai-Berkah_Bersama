<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin(); 

$pesan = "";
$tipe_pesan = "";

// Cek ID
if (!isset($_GET['id'])) { header("Location: data_nasabah.php"); exit; }
$id_nasabah = $_GET['id'];

// PROSES SIMPAN PERUBAHAN (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = $_POST['nama_lengkap'];
    $jabatan  = $_POST['id_jabatan'];
    $username = $_POST['username'];
    $status   = $_POST['status'];
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $sql = "UPDATE nasabah SET id_jabatan=?, nama_lengkap=?, username=?, status=?, hashed_password=? WHERE id_nasabah=?";
            $params = [$jabatan, $nama, $username, $status, $password, $id_nasabah];
        } else {
            $sql = "UPDATE nasabah SET id_jabatan=?, nama_lengkap=?, username=?, status=? WHERE id_nasabah=?";
            $params = [$jabatan, $nama, $username, $status, $id_nasabah];
        }

        $pdo->prepare($sql)->execute($params);
        $pesan = "Data berhasil diperbarui!";
        $tipe_pesan = "success";
    } catch (Exception $e) {
        $pesan = "Gagal Update: " . $e->getMessage();
        $tipe_pesan = "error";
    }
}

// AMBIL DATA LAMA
$stmt = $pdo->prepare("SELECT * FROM nasabah WHERE id_nasabah = ?");
$stmt->execute([$id_nasabah]);
$data = $stmt->fetch();
if (empty($data)) { die("Data nasabah tidak ditemukan!"); }

// AMBIL LIST JABATAN
$list_jabatan = $pdo->query("SELECT * FROM jabatan")->fetchAll();
?>

<?php if (!empty($pesan)): ?>
    <div class="msg-box <?php echo $tipe_pesan; ?>"><?php echo $pesan; ?></div>
<?php endif; ?>

<div class="dashboard-content">
    
    <div class="dashboard-welcome">
        <h2>✏️ Edit Data Nasabah</h2>
        <span>Perbarui informasi nasabah di bawah ini</span>
    </div>

    <div class="widget-box">
        <div class="widget-header">
            <span>Formulir Edit Nasabah</span>
        </div>
        <div class="widget-body">
            <form method="POST" action="">
                <div class="admin-form-row">
                    <label>ID Nasabah</label>
                    <input type="text" value="<?php echo $data['id_nasabah']; ?>" disabled style="background:#f1f5f9; color:#64748b; border-color:#e2e8f0;">
                </div>

                <div class="admin-form-row">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" required>
                </div>
                
                <div class="admin-form-row">
                    <label>Jabatan</label>
                    <select name="id_jabatan">
                        <?php foreach($list_jabatan as $jab): ?>
                            <option value="<?php echo $jab['id_jabatan']; ?>" 
                                <?php echo ($data['id_jabatan'] == $jab['id_jabatan']) ? 'selected' : ''; ?>>
                                <?php echo $jab['nama_jabatan']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="admin-form-row">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
                </div>

                <div class="admin-form-row">
                    <label>Status</label>
                    <select name="status">
                        <option value="AKTIF" <?php echo ($data['status'] == 'AKTIF') ? 'selected' : ''; ?>>AKTIF</option>
                        <option value="SUSPEND" <?php echo ($data['status'] == 'SUSPEND') ? 'selected' : ''; ?>>SUSPEND</option>
                    </select>
                </div>

                <div class="admin-form-row">
                    <label>Password Baru</label>
                    <input type="password" name="password" placeholder="(Isi hanya jika ingin ganti password)">
                </div>
                
                <div class="admin-btn-container">
                    <button type="submit" class="btn-admin-action">Simpan Perubahan</button>
                    <a href="data_nasabah.php" class="btn-admin-action" style="background:#fff; color:#64748b; border:1px solid #cbd5e1; box-shadow:none;">Kembali</a>
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>