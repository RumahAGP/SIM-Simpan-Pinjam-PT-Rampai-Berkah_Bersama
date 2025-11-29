<?php
require_once '../config/database.php';
require_once '../includes/header.php';
cekLoginNasabah(); 

$id_nasabah = $_SESSION['user_id'];

// 1. Ambil daftar pinjaman (Join status agar bisa cek LUNAS/BELUM)
$query_list = "SELECT p.*, sp.status 
               FROM pinjaman p
               LEFT JOIN status_pinjaman sp ON p.id_pinjaman = sp.id_pinjaman
               WHERE p.id_nasabah = :id 
               ORDER BY p.id_pinjaman DESC";
$stmt = $pdo->prepare($query_list);
$stmt->execute([':id' => $id_nasabah]);
$list_pinjaman = $stmt->fetchAll();

// 2. Ambil data angsuran untuk detail modal & hitung jumlah bayar
$query_bayar = "SELECT a.*, m.nama_metode_pembayaran, p.nominal_pinjaman
                FROM angsuran a
                JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman
                LEFT JOIN metode_pembayaran m ON a.id_metode_pembayaran = m.id_metode_pembayaran
                WHERE p.id_nasabah = :id";
$stmt = $pdo->prepare($query_bayar);
$stmt->execute([':id' => $id_nasabah]);
$raw_angsuran = $stmt->fetchAll();

// 3. Grouping data angsuran
$data_angsuran = [];
foreach ($raw_angsuran as $bayar) {
    $id_p = $bayar['id_pinjaman'];
    $data_angsuran[$id_p][$bayar['angsuran_ke']] = $bayar;
}
// Kirim ke JS
$json_angsuran = json_encode($data_angsuran);
?>

<div class="dashboard-content">
    <div class="widget-box">
        <div class="widget-header">
            <span>Riwayat Pembayaran Pinjaman</span>
            <button class="btn-admin-action" style="background:var(--info-color);" onclick="window.print()">🖨️ Cetak</button>
        </div>
        <div class="widget-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">ID</th>
                            <th>Nominal Pinjaman</th>
                            <th>Alasan</th>
                            <th>Tenor</th>
                            <th>Tanggal Pengajuan</th>
                            <th style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list_pinjaman as $row): ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $row['id_pinjaman']; ?></td>
                            <td>Rp <?php echo formatRupiah($row['nominal_pinjaman']); ?></td>
                            <td><?php echo htmlspecialchars($row['alasan_pengajuan']); ?></td>
                            <td style="text-align:center;"><?php echo $row['tenor']; ?> Bulan</td>
                            <td style="text-align:center;"><?php echo date('d-m-Y', strtotime($row['tgl_pengajuan'])); ?></td>
                            
                            <td style="text-align:center;">
                                <?php 
                                    // Hitung jumlah angsuran yang sudah masuk
                                    $jumlah_bayar = count($data_angsuran[$row['id_pinjaman']] ?? []);
                                    
                                    // Cek Lunas: Jika status di DB 'LUNAS' ATAU jumlah bayar >= tenor
                                    $is_lunas = ($row['status'] === 'LUNAS') || ($jumlah_bayar >= $row['tenor']);
                                ?>

                                <?php if ($is_lunas): ?>
                                    <span class="status-badge badge-active">
                                        ✔ LUNAS
                                    </span>
                                <?php else: ?>
                                    <button class="btn-admin-action" style="background:var(--primary-color); padding: 4px 10px; width: auto; font-size: 11px;" 
                                        onclick="showDetail(
                                            '<?php echo $row['id_pinjaman']; ?>',
                                            '<?php echo $row['tenor']; ?>',
                                            '<?php echo $row['nominal_pinjaman']; ?>'
                                        )">
                                        Lihat
                                    </button>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($list_pinjaman)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:20px;">Tidak ada data pinjaman.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="detailModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 750px;">
        <div class="modal-header">
            <span id="modalTitle" style="font-weight:bold;">Detail Angsuran</span>
            <span class="close-btn" onclick="closeModal('detailModal')">×</span>
        </div>

        <div class="modal-body">
            <table class="custom-table" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>No Trans</th>
                        <th>Ke</th>
                        <th>Wajib Bayar</th>
                        <th>Dibayar</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="modalContent"></tbody>
            </table>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-admin-action" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
    const dataAngsuran = <?php echo $json_angsuran; ?>;

    function showDetail(idPinjaman, tenor, nominalTotal) {
        document.getElementById('modalTitle').innerText = "Detail Angsuran Pinjaman ID: " + idPinjaman;
        const tbody = document.getElementById('modalContent');
        tbody.innerHTML = "";

        const tagihanPerBulan = Math.round(nominalTotal / tenor);
        const payments = dataAngsuran[idPinjaman] || {};

        for (let i = 1; i <= tenor; i++) {
            const isPaid = payments[i] !== undefined;
            const dataBayar = isPaid ? payments[i] : null;

            const displayNominalWajib = 'Rp ' + tagihanPerBulan.toLocaleString('id-ID');
            const displayNominalBayar = isPaid ? 'Rp ' + parseInt(dataBayar.nominal_angsuran).toLocaleString('id-ID') : '-';
            const displayTanggal = isPaid ? dataBayar.tgl_pembayaran.substring(0, 10) : "-";
            const displayIdTrans = isPaid ? dataBayar.id_angsuran : "-";
            const statusText = isPaid ? "LUNAS" : "BELUM";
            const statusColor = isPaid ? "green" : "red";
            const rowBg = isPaid ? "#e8f5e9" : "#fff";

            let row = document.createElement('tr');
            row.style.backgroundColor = rowBg;
            
            row.innerHTML = `
                <td style="text-align:center;">${displayIdTrans}</td>
                <td style="text-align:center;">${i}</td>
                <td style="text-align:right;">${displayNominalWajib}</td>
                <td style="text-align:right; font-weight:bold;">${displayNominalBayar}</td>
                <td style="text-align:center;">${displayTanggal}</td>
                <td style="color:${statusColor}; font-weight:bold; text-align:center;">${statusText}</td>
            `;
            tbody.appendChild(row);
        }

        document.getElementById('detailModal').style.display = "block";
    }
    
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>

<?php require_once '../includes/footer.php'; ?>