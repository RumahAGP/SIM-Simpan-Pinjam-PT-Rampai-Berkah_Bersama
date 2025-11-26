/* js/script.js */

// =========================================
// 1. LOGIKA CHART (DASHBOARD)
// =========================================

function initDashboardChart(labels, dSimpan, dPinjam, dBayar) {
    // PERBAIKAN: ID disesuaikan menjadi 'chart-keuangan'
    const ctx = document.getElementById('chart-keuangan'); 
    
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line', // Menggunakan Line Chart untuk tren
            data: {
                labels: labels,
                datasets: [
                    { 
                        label: 'Simpanan', 
                        data: dSimpan, 
                        borderColor: '#28a745', // Hijau
                        backgroundColor: 'rgba(40, 167, 69, 0.1)', 
                        borderWidth: 2, 
                        tension: 0.4, 
                        fill: true 
                    },
                    { 
                        label: 'Pinjaman', 
                        data: dPinjam, 
                        borderColor: '#dc3545', // Merah
                        backgroundColor: 'rgba(220, 53, 69, 0.1)', 
                        borderWidth: 2, 
                        tension: 0.4, 
                        fill: true 
                    },
                    { 
                        label: 'Pembayaran', 
                        data: dBayar, 
                        borderColor: '#17a2b8', // Biru
                        backgroundColor: 'rgba(23, 162, 184, 0.1)', 
                        borderWidth: 2, 
                        tension: 0.4, 
                        fill: true 
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                interaction: { mode: 'index', intersect: false },
                scales: { 
                    y: { beginAtZero: true, ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } } }
                }
            }
        });
    }
}

// B. Grafik Status Pinjaman (Pie/Doughnut Chart)
function initPieChart(labels, dataValues) {
    // ID: 'chart-status'
    const ctx = document.getElementById('chart-status');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8'], // Hijau, Kuning, Merah, Biru
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'right' } 
                },
                cutout: '60%'
            }
        });
    }
}

// =========================================
// 2. LOGIKA MODAL & GLOBAL FUNCTIONS
// =========================================

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = "none";
    }
}

// --- MODAL FUNCTIONS ---

function openLookupNasabah() { document.getElementById('lookupModal').style.display = 'block'; }
function openLookupPinjaman() { document.getElementById('lookupModal').style.display = 'block'; }
function openModalPinjaman() { document.getElementById('loanModal').style.display = 'block'; }

function openEditNasabah(btn) {
    const m = document.getElementById('editModal');
    document.getElementById('modal_id').value = btn.getAttribute('data-id');
    document.getElementById('modal_nama').value = btn.getAttribute('data-nama');
    document.getElementById('modal_user').value = btn.getAttribute('data-user');
    document.getElementById('modal_jabatan').value = btn.getAttribute('data-jabatan');
    document.getElementById('modal_status').value = btn.getAttribute('data-status');
    document.getElementById('modal_pass').value = "";
    m.style.display = 'block';
}

// Pilih Nasabah (Simpanan) - Klik Langsung Pilih
function selectRowNasabah(id, name) {
    document.getElementById('inputNasabah').value = name;
    document.getElementById('idNasabahHidden').value = id;
    closeModal('lookupModal');
}

// Pilih Pinjaman (Pembayaran) - Klik Langsung Submit
function selectRowPinjaman(id) {
    document.getElementById('inputIdPinjaman').value = id;
    const form = document.getElementById('searchForm');
    if(form) form.submit();
    closeModal('lookupModal');
}

function confirmDelete() { return confirm("Yakin hapus data ini?"); }