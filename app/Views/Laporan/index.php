<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
  
    <title>Laporan Kas Masjid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: sans-serif;
            background: #f6f6e9;
            margin: 0;
            padding: 0;
            display: flex;
        }
        /* Sidebar */
        .sidebar {
            width: 220px;
            background-color: #7d9b76;
            height: 100vh;
            padding: 2em 1em;
            color: white;
            position: fixed;
        }
        .sidebar h2 { font-size: 1.2em; margin-bottom: 2em; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 1em; }
        .sidebar ul li a {
            color: white; text-decoration: none; display: block;
            padding: 0.5em 1em; border-radius: 10px;
        }
        .sidebar ul li a.active, .sidebar ul li a:hover {
            background-color: #cbddd1; color: black;
        }
        /* Content */
        .content {
            margin-left: 240px;
            padding: 2em;
            width: 100%;
        }
        h1 { font-size: 1.8em; margin-bottom: 0.5em; }
        .add-btn {
            font-size: 0.75em; 
            padding: 0.4em 0.8em; 
            border-radius: 20px;
            background: #333;
            color: white;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            font-size: 0.9em;
        }
        table th, table td {
            padding: 0.6em 0.8em;
            border-bottom: 1px solid #ddd;
        }
        table th { background: #eee; }
        .btn-action {
            padding: 0.4em 1.2em;
            border: none;
            border-radius: 20px;
            background: #333;
            color: white;
            cursor: pointer;
            text-decoration: none;
            margin-right: 5px;
            font-size: 0.85em;
        }
        .btn-action i { margin-right: 5px; }
        .btn-action.btn-danger { background: #dc3545; }
        .btn-action.btn-info { background: #333; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Keuangan Masjid<br>Al-Anshor</h2>
    <ul>
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="/pemasukan">Pendapatan</a></li>
        <li><a href="/pengeluaran">Pengeluaran</a></li>
        <li><a href="/laporan" class="active">Laporan</a></li>
        <li><a href="/logout">Logout</a></li>
    </ul>
</div>

<div class="content">
    <h1>Laporan Kas Masjid</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('laporan/buat') ?>" method="post" class="mb-3">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-md-4">
                <label>Periode Awal</label>
                <input type="date" name="tanggal_awal" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Periode Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="add-btn">+ Buat Laporan</button>
            </div>
        </div>
    </form>

    <hr>

    <table id="tabelLaporan" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Periode</th>
                <th>Saldo Awal</th>
                <th>Saldo Akhir</th>
                <th>Nama File</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporan)): ?>
                <?php $no = 1; foreach ($laporan as $lap): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($lap['tanggal_awal'])) ?> - <?= date('d/m/Y', strtotime($lap['tanggal_akhir'])) ?></td>
                        <td>Rp <?= number_format($lap['saldo_awal'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($lap['saldo_akhir'], 0, ',', '.') ?></td>
                        <td><?= esc($lap['nama_file']) ?></td>
                        <td>
                            <a href="<?= base_url('laporan/download/' . $lap['nama_file']) ?>" target="_blank" class="btn-action btn-info">
                                <i class="bi bi-printer"></i> Cetak
                            </a>
                            <br>
                            <a href="<?= base_url('laporan/hapus/' . $lap['id']) ?>" 
                               class="btn-action btn-danger"
                               onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#tabelLaporan').DataTable({
        "language": {
            "emptyTable": "Belum ada laporan disimpan"
        }
    });
});
</script>
</body>
</html>
