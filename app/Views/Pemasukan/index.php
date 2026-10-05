<?php $role = session()->get('role'); ?>
<?php $bendaharaRoles = $bendaharaRoles ?? ['bendahara', 'bendahara_umum', 'bendahara_kas_kecil']; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
   
    <title>Data Pendapatan Masjid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body { 
            font-family: sans-serif; 
            background: #f6f6e9; 
            margin: 0; padding: 0; 
            display: flex; }
        .sidebar { width: 220px; background-color: #7d9b76; height: 100vh; padding: 2em 1em; color: white; position: fixed; }
        .sidebar h2 { font-size: 1.2em; margin-bottom: 2em; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1em; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; padding: 0.5em 1em; border-radius: 10px; }
        .sidebar ul li a.active, .sidebar ul li a:hover { background-color: #cbddd1; color: black; }
        .content { margin-left: 240px; padding: 2em; width: 100%; }
        h1 { font-size: 1.8em; margin-bottom: 0.5em; }
        .total { font-size: 1.5em; font-weight: bold; margin-bottom: 1em; }
        .add-btn { font-size: 0.75em; padding: 0.4em 0.8em; border-radius: 20px; background: #333; color: white; cursor: pointer; text-decoration: none; border: none; }
        .btn-table { padding: 0.6em 1.2em; border: none; border-radius: 20px; background: #333; color: white; cursor: pointer; text-decoration: none; margin-right: 5px; font-size: 0.85em; }
        .btn-table i { margin-right: 5px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; font-size: 0.9em; }
        table th, table td { padding: 0.6em 0.8em; border-bottom: 1px solid #ddd; }
        table th { background: #eee; }
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.5); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 2em; border-radius: 10px; width: 50%; }
        .modal-content input, .modal-content textarea { width: 100%; padding: 0.6em; margin-bottom: 1em; border: 1px solid #ccc; border-radius: 10px; }
        .modal-content button { padding: 0.6em 1.2em; border: none; border-radius: 20px; background: #333; color: white; cursor: pointer; }
        .close { float: right; font-size: 1.2em; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Keuangan Masjid<br>Al-Anshor</h2>
    <ul>
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="/pemasukan" class="active">Pendapatan</a></li>
        <li><a href="/pengeluaran">Pengeluaran</a></li>
        <li><a href="/laporan">Laporan</a></li>
        <li><a href="/logout">Logout</a></li>
    </ul>
</div>

<div class="content">
    <h1>Data Pendapatan Masjid</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="total">
        Total Saldo: Rp <?= number_format($totalSaldo, 0, ',', '.') ?>
    </div>

    <?php if (in_array($role, $bendaharaRoles)): ?>
        <button type="button" class="add-btn mb-3" onclick="openTambahModal()">+ Tambah Data</button>
    <?php endif; ?>
<hr>
    <table id="tabelData" class="display">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Jenis</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pemasukan as $item): ?>
            <tr>
                <td><?= date('d-m-Y', strtotime($item['tanggal'])) ?></td>
                <td><?= $item['jumlah_jenis'] ?></td>
                <td>Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                <td>
                    <button class="btn-table" onclick="openDetailModal('<?= $item['tanggal'] ?>')"><i class="bi bi-clipboard2"></i>Detail</button>
                    <?php if (in_array($role, $bendaharaRoles)): ?>
                        <button class="btn-table" onclick="openEditModal('<?= $item['tanggal'] ?>')"><i class="bi bi-pencil-fill"></i>Edit</button>
                        <form action="/pemasukan/delete/<?= $item['tanggal'] ?>" method="post" onsubmit="return confirm('Hapus semua data di tanggal ini?')" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-table" style="background:#dc3545;"><i class="bi bi-trash"></i>Hapus</button>
                        </form>
                    <?php else: ?>
                        <button class="btn-table disabled" disabled>Edit</button>
                        <button class="btn-table disabled" disabled>Hapus</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDetailModal()">&times;</span>
        <h3>Detail Pendapatan</h3>
        <div id="detailContent"></div>
    </div>
</div>

<!-- Modal Tambah -->
<?php if (in_array($role, $bendaharaRoles)): ?>
<div id="tambahModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeTambahModal()">&times;</span>
        <h3>Tambah Pendapatan</h3>
        <form method="post" action="/pemasukan/store">
            <?= csrf_field() ?>
            <input type="date" name="tanggal" required>
            <div id="pemasukanContainer">
                <div class="row mb-2 pemasukan-item">
                    <div class="col-md-4"><input type="text" name="jenis[]" class="form-control" placeholder="Jenis Pemasukan" required></div>
                    <div class="col-md-3"><input type="text" name="jumlah[]" class="form-control" placeholder="Jumlah (Rp)" required oninput="this.value = formatRupiah(this.value)"></div>
                    <div class="col-md-4"><input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove" onclick="hapusBaris(this)">X</button></div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="tambahBaris()">+ Tambah Baris</button>
            <br><br>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modal Edit -->
<?php if (in_array($role, $bendaharaRoles)): ?>
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Pendapatan</h3>
        <form method="post" id="editForm">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit-id">
            <input type="date" name="tanggal" id="edit-tanggal" class="form-control mb-2" required>
            <div id="pemasukanContainerEdit"></div>
            <button type="button" class="btn btn-success mt-2" onclick="tambahBarisEdit()">Tambah Baris</button>
            <button type="submit" class="btn btn-primary mt-2">Update</button>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(() => $('#tabelData').DataTable({ "pageLength": 10 }));

function openDetailModal(tanggal) {
    fetch(`/pemasukan/detail/${tanggal}`)
    .then(res => res.json())
    .then(data => {
        let html = "<ul>";
        data.forEach(item => {
            html += `<li>${item.jenis} - Rp ${formatRupiah(item.jumlah.toString())} (${item.keterangan || '-'})</li>`;
        });
        html += "</ul>";
        document.getElementById('detailContent').innerHTML = html;
        document.getElementById('detailModal').style.display = "block";
    });
}
function closeDetailModal() { document.getElementById('detailModal').style.display = "none"; }

function openTambahModal() { document.getElementById("tambahModal").style.display = "block"; }
function closeTambahModal() { document.getElementById("tambahModal").style.display = "none"; }

function openEditModal(tanggal) {
    fetch(`/pemasukan/detail/${tanggal}`)
    .then(res => res.json())
    .then(dataArray => {
        if (!dataArray.length) return;
        document.getElementById("edit-tanggal").value = tanggal;
        document.getElementById("editForm").action = "/pemasukan/update/" + tanggal;
        const container = document.getElementById("pemasukanContainerEdit");
        container.innerHTML = "";
        dataArray.forEach(item => tambahBarisEdit(item.jenis, formatRupiah(item.jumlah.toString()), item.keterangan || ''));
        document.getElementById("editModal").style.display = "block";
    });
}
function closeEditModal() { document.getElementById("editModal").style.display = "none"; }

function tambahBaris() {
    const container = document.getElementById('pemasukanContainer');
    const item = document.createElement('div');
    item.classList.add('row', 'mb-2', 'pemasukan-item');
    item.innerHTML = `
        <div class="col-md-4"><input type="text" name="jenis[]" class="form-control" placeholder="Jenis Pemasukan" required></div>
        <div class="col-md-3"><input type="text" name="jumlah[]" class="form-control" placeholder="Jumlah (Rp)" required oninput="this.value = formatRupiah(this.value)"></div>
        <div class="col-md-4"><input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan"></div>
        <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove" onclick="hapusBaris(this)">X</button></div>`;
    container.appendChild(item);
}

function tambahBarisEdit(jenis = '', jumlah = '', keterangan = '') {
    const container = document.getElementById('pemasukanContainerEdit');
    const item = document.createElement('div');
    item.classList.add('row', 'mb-2', 'pemasukan-item');
    item.innerHTML = `
        <div class="col-md-4"><input type="text" name="jenis[]" class="form-control" value="${jenis}" required></div>
        <div class="col-md-3"><input type="text" name="jumlah[]" class="form-control" value="${jumlah}" required oninput="this.value = formatRupiah(this.value)"></div>
        <div class="col-md-4"><input type="text" name="keterangan[]" class="form-control" value="${keterangan}"></div>
        <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove" onclick="hapusBaris(this)">X</button></div>`;
    container.appendChild(item);
}

function hapusBaris(btn) { btn.closest('.pemasukan-item').remove(); }

window.onclick = function(event) {
    ['tambahModal','editModal','detailModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (event.target === modal) modal.style.display = "none";
    });
}

function formatRupiah(angka) {
    let number_string = angka.replace(/[^,\d]/g, "").toString(),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) rupiah += (sisa ? "." : "") + ribuan.join(".");
    return split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
}
</script>
</body>
</html>
