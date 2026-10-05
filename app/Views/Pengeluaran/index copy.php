<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Pengeluaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: sans-serif;
        background: #f6f6e9;
        margin: 0;
        padding: 0;
        display: flex;
    }


    .sidebar {
        width: 220px;
        background-color: #7d9b76;
        height: 100vh;
        padding: 2em 1em;
        color: white;
        position: fixed;
    }

    .sidebar h2 {
        font-size: 1.2em;
        margin-bottom: 2em;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin-bottom: 1em;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 0.5em 1em;
        border-radius: 10px;
    }

    .sidebar ul li a.active,
    .sidebar ul li a:hover {
        background-color: #cbddd1;
        color: black;
    }

    .content {
        margin-left: 240px;
        padding: 2em;
        width: 100%;
    }

    h1 {
        font-size: 1.8em;
        margin-bottom: 0.5em;
    }

    .total {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 1em;
    }

    .search-bar {
        margin-bottom: 1.5em;
    }

    .search-bar input {
        padding: 0.5em;
        border-radius: 10px;
        border: 1px solid #ccc;
        width: 300px;
    }

    .add-btn {
        background: #333;
        color: white;
        padding: 0.5em 1.2em;
        text-decoration: none;
        border-radius: 20px;
        margin-left: 1em;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        font-size: 0.9em;
    }

    table th,
    table td {
        padding: 1em;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    table th {
        background: #eee;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 2em;
        border-radius: 10px;
        width: 50%;
    }

    .modal-content input,
    .modal-content textarea {
        width: 100%;
        padding: 0.6em;
        margin-bottom: 1em;
        border: 1px solid #ccc;
        border-radius: 10px;
    }

    .modal-content button {
        padding: 0.6em 1.2em;
        border: none;
        border-radius: 20px;
        background: #333;
        color: white;
        cursor: pointer;
    }

    .close {
        float: right;
        font-size: 1.2em;
        cursor: pointer;
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Keuangan Masjid<br>Al-Anshor</h2>
        <ul>
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/pemasukan">Pemasukan</a></li>
            <li><a href="/pengeluaran" class="active">Pengeluaran</a></li>
            <li><a href="/logout">Logout</a></li>
        </ul>
    </div>

    <!-- Konten -->
    <div class="content">
        <h1>Data Pengeluaran Masjid</h1>

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="search-bar">
            <form method="get" action="/pemasukan">
                <input type="text" name="keyword" placeholder="Cari jenis, tanggal, atau sub jenis..."
                    value="<?= esc($keyword) ?>">
                <button type="submit">Cari</button>
                <button type="button" class="add-btn" onclick="openTambahModal()">+ Tambah Data</button>
            </form>
        </div>

        <!-- Tabel Pengeluaran -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Saldo Awal</th>
                    <th>Total Pemasukan</th>
                    <th>Total Pengeluaran</th>
                    <th>Saldo Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pengeluaran)): ?>
                <?php foreach ($pengeluaran as $p): ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($p['periode_awal'])) ?> s.d
                        <?= date('d-m-Y', strtotime($p['periode_akhir'])) ?></td>
                    <td>Rp <?= number_format($p['saldo_awal'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($p['total_pemasukan'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($p['total_pengeluaran'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($p['saldo_akhir'], 0, ',', '.') ?></td>
                    <td>
                        <button onclick="showDetail(<?= $p['id'] ?>)">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <a href="/pengeluaran/pdf/<?= $p['id'] ?>" class="btn btn-sm btn-info">
                            <i class="bi bi-file-pdf"></i> PDF
                        </a>
                        <form action="/pengeluaran/hapus/<?= $p['id'] ?>" method="post" style="display:inline;"
                            onsubmit="return confirm('Yakin ingin menghapus?')">
                            <?= csrf_field() ?>
                            <button type="submit" style="background:none; border:none; color:red; cursor:pointer;">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </td>

                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6">Tidak ada data pengeluaran.</td>
                </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form class="modal-content" method="post" action="/pengeluaran/simpan">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Data Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label>Periode Mulai</label>
                            <input type="date" name="periode_awal" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>Periode Selesai</label>
                            <input type="date" name="periode_akhir" class="form-control" required>
                        </div>
                    </div>


                    <h6>Detail Pengeluaran</h6>
                    <div id="pengeluaranContainer">
                        <div class="row mb-2 pengeluaran-item">
                            <div class="col-md-4">
                                <input type="text" name="jenis[]" class="form-control" placeholder="Jenis Pengeluaran"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah (Rp)"
                                    required oninput="this.value = formatRupiah(this.value)">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-remove">X</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="tambahBaris()">+ Tambah
                        Baris</button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Pengeluaran -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Laporan Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Periode:</strong> <span id="periodeDetail"></span></p>
                    <p><strong>Saldo Awal:</strong> Rp <span id="saldoAwalDetail"></span></p>

                    <hr>
                    <h6>Detail Pemasukan</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Transaksi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="detailPemasukanBody"></tbody>
                        <tfoot>
                            <tr>
                                <th>Total Pemasukan</th>
                                <th>Rp <span id="totalPemasukanDetail"></span></th>
                            </tr>
                        </tfoot>
                    </table>

                    <hr>
                    <h6>Detail Pengeluaran</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Jenis Pengeluaran</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="detailPengeluaranBody"></tbody>
                        <tfoot>
                            <tr>
                                <th>Total Pengeluaran</th>
                                <th>Rp <span id="totalPengeluaranDetail"></span></th>
                            </tr>
                        </tfoot>
                    </table>

                    <hr>
                    <p><strong>Saldo Akhir:</strong> Rp <span id="saldoAkhirDetail"></span></p>
                </div>
                <div class="modal-footer">
                    <a href="#" id="btnCetakDetail" class="btn btn-success" target="_blank"><i class="fas fa-print"></i>
                        Cetak PDF</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function tambahBaris() {
        const container = document.getElementById('pengeluaranContainer');
        const item = document.querySelector('.pengeluaran-item');
        const clone = item.cloneNode(true);
        clone.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(clone);
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove')) {
            const row = e.target.closest('.pengeluaran-item');
            const allRows = document.querySelectorAll('.pengeluaran-item');
            if (allRows.length > 1) {
                row.remove();
            }
        }
    });

    function formatRupiah(angka) {
        let cleaned = angka.replace(/\D/g, '');
        return cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[name="jumlah[]"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = formatRupiah(this.value);
            });
        });
    });

    function showDetail(id) {
        fetch(`/pengeluaran/detail/${id}`)
            .then(res => res.json())
            .then(data => {
                let content = `
                    <p><strong>Periode:</strong> ${formatTanggal(data.periode_awal)} s.d ${formatTanggal(data.periode_akhir)}</p>
                    <p><strong>Saldo Sebelumnya:</strong> Rp ${formatAngka(data.saldo_awal)}</p>
                    <p><strong>Total Pemasukan:</strong> Rp ${formatAngka(data.total_pemasukan)}</p>
                    <p><strong>Total Pengeluaran:</strong> Rp ${formatAngka(data.total_pengeluaran)}</p>
                    <hr>
                    <h5>Rincian Pengeluaran</h5>
                    <ul>
                `;

                data.detail.forEach(item => {
                    content += `
                        <li>
                            ${item.jenis} - Rp ${formatAngka(item.jumlah)} 
                            <br><small>${item.keterangan ?? ''}</small>
                        </li>
                    `;
                });

                content += `
                    </ul>
                    <hr>
                    <p><strong>Saldo Akhir:</strong> Rp ${formatAngka(data.saldo_akhir)}</p>
                `;

                document.getElementById('detailContent').innerHTML = content;
                document.getElementById('detailModal').style.display = 'block';
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    function formatAngka(angka) {
        return parseInt(angka).toLocaleString('id-ID');
    }

    function formatTanggal(tanggal) {
        const date = new Date(tanggal);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    // Tutup modal jika klik luar
    window.onclick = function(event) {
        const modal = document.getElementById('detailModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function showDetail(id) {
        $.get('/pengeluaran/detail/' + id, function(data) {
            $('#periodeDetail').text(
                new Date(data.periode_awal).toLocaleDateString('id-ID') + ' s.d ' +
                new Date(data.periode_akhir).toLocaleDateString('id-ID')
            );
            $('#saldoAwalDetail').text(formatRupiah(data.saldo_awal));
            $('#totalPemasukanDetail').text(formatRupiah(data.total_pemasukan));
            $('#totalPengeluaranDetail').text(formatRupiah(data.total_pengeluaran));
            $('#saldoAkhirDetail').text(formatRupiah(data.saldo_akhir));

            let pemasukanHTML = '';
            data.detail_pemasukan.forEach(item => {
                pemasukanHTML += `<tr>
        <td>${item.nama}</td>
        <td>Rp ${formatRupiah(item.jumlah)}</td>
      </tr>`;
            });
            $('#detailPemasukanBody').html(pemasukanHTML);

            let pengeluaranHTML = '';
            data.detail_pengeluaran.forEach(item => {
                pengeluaranHTML += `<tr>
        <td>${item.jenis}</td>
        <td>Rp ${formatRupiah(item.jumlah)}</td>
      </tr>`;
            });
            $('#detailPengeluaranBody').html(pengeluaranHTML);

            $('#btnCetakDetail').attr('href', '/pengeluaran/cetak/' + id);

            const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
            modal.show();
        });
    }
    </script>


</body>



</html>