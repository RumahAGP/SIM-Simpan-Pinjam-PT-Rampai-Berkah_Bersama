<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginAdmin(); 

$pesan = "";
$tipe_pesan = "";
$input_nama = "";
$input_username = "";
$input_jabatan = "";

// --- LOGIKA REGISTER NASABAH ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_nama     = $_POST['nama_lengkap'] ?? '';
    $input_jabatan  = $_POST['id_jabatan'] ?? '';
    $input_username = $_POST['username'] ?? '';
    $password       = $_POST['password'] ?? '';

    if (empty($input_nama) || empty($input_jabatan) || empty($input_username) || empty($password)) {
        $pesan = "Gagal: Semua kolom wajib diisi!";
        $tipe_pesan = "error";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT username FROM nasabah WHERE username = ?");
            $stmt->execute([$input_username]);
            if ($stmt->fetch()) {
                $pesan = "Gagal: Username '$input_username' sudah digunakan!";
                $tipe_pesan = "error";
            } else {
                $query = "INSERT INTO nasabah (id_jabatan, nama_lengkap, username, hashed_password, status) 
                          VALUES (?, ?, ?, ?, 'AKTIF')";
                $pdo->prepare($query)->execute([$input_jabatan, $input_nama, $input_username, $password]);

                $pesan = "Berhasil! Akun nasabah baru telah dibuat.";
                $tipe_pesan = "success";
                $input_nama = $input_username = $input_jabatan = "";
            }
        } catch (Exception $e) {
            $pesan = "Terjadi Kesalahan Database: " . $e->getMessage();
            $tipe_pesan = "error";
        }
    }
}

$list_jabatan = $pdo->query("SELECT * FROM jabatan ORDER BY nama_jabatan ASC")->fetchAll();
?>

<?php if (!empty($pesan)): ?>
    <div class="msg-box <?php echo $tipe_pesan; ?>">
        <?php echo $pesan; ?>
    </div>
<?php endif; ?>

<div class="window-panel">
    <div class="window-header-strip"><div class="window-icon"></div></div>
    <div class="form-wrapper" style="max-width: 600px; margin: 0 auto;">
        
        <form method="POST" action="">
            
            <div class="admin-form-row">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama" value="<?php echo htmlspecialchars($input_nama); ?>" required>
            </div>
            
            <div class="admin-form-row">
                <label for="jabatan">Jabatan</label>
                <select name="id_jabatan" id="jabatan" required>
                    <option value="" disabled <?php echo empty($input_jabatan) ? 'selected' : ''; ?>>-- Pilih Jabatan --</option>
                    <?php foreach($list_jabatan as $jab): ?>
                        <option value="<?php echo $jab['id_jabatan']; ?>" <?php echo ($input_jabatan == $jab['id_jabatan']) ? 'selected' : ''; ?>>
                            <?php echo $jab['nama_jabatan']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="admin-form-row">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($input_username); ?>" autocomplete="off" required>
            </div>
            
            <div class="admin-form-row">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div class="admin-btn-container">
                <button type="submit" class="btn-admin-action">Register</button>
                <a href="tambah_nasabah.php" class="btn-admin-action" style="background:#d9534f; text-decoration:none; text-align:center; line-height:normal;">Batal</a>
            </div>
            
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; 