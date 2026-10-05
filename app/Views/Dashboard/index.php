<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30">
    <title>Dashboard Masjid Al-Anshor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: sans-serif;
            background: #f6f6e9;
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

        .card-click {
            cursor: pointer;
            transition: 0.3s;
        }

        .card-click:hover {
            transform: scale(1.03);
        }

        .chart-container {
            display: none;
            margin-top: 2em;
        }

        .chart-container.active {
            display: block;
        }

        .bg-card {
            background-color: #457d58 !important;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Keuangan Masjid<br>Al-Anshor</h2>
        <ul>
            <li><a href="/dashboard" class="active">Dashboard</a></li>
            <li><a href="/pemasukan">Pemasukan</a></li>
            <li><a href="/pengeluaran">Pengeluaran</a></li>
            <li><a href="/laporan">Laporan</a></li>
            <li><a href="/logout">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h1 class="mb-4">Keuangan Masjid Al-Anshor</h1>

        <div class="d-flex justify-content-end align-items-center mb-3">
            <label for="filterPeriode" class="me-2">Filter Periode:</label>
            <select id="filterPeriode" class="form-select w-auto">
                <option value="minggu">Minggu Ini</option>
                <option value="bulan">Bulan Ini</option>
                <option value="tahun">Tahun Ini</option>
                <option value="semua">Semua</option>
            </select>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card card-click" data-chart="saldoChart">
                    <div class="card-body text-center bg-card text-white rounded">
                        <h5 class="card-title">Saldo Saat Ini</h5>
                        <p class="card-text">Rp <?= number_format($saldoSaatIni, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-click" data-chart="pemasukanChart">
                    <div class="card-body text-center bg-card text-white rounded">
                        <h5 class="card-title">Total Pemasukan</h5>
                        <p class="card-text">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-click" data-chart="pengeluaranChart">
                    <div class="card-body text-center bg-card text-white rounded">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <p class="card-text">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center bg-card text-white rounded">
                        <h5 class="card-title">Jumlah Transaksi</h5>
                        <p class="card-text"><?= $jumlahTransaksi ?> Transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART -->
        <div id="saldoChart" class="chart-container active">
            <canvas id="barSaldoChart"></canvas>
        </div>
        <div id="pemasukanChart" class="chart-container">
            <canvas id="barPemasukanJenis"></canvas>
        </div>
        <div id="pengeluaranChart" class="chart-container">
            <canvas id="barPengeluaranJenis"></canvas>
        </div>
    </div>

    <script>
        // Data dari backend
        const tanggal = <?= json_encode($tanggalTransaksi) ?>;
        const transaksiPemasukan = <?= json_encode($jumlahTransaksiPemasukan) ?>;
        const transaksiPengeluaran = <?= json_encode($jumlahTransaksiPengeluaran) ?>;
        const pemasukanData = <?= json_encode($piePemasukan) ?>;
        const pengeluaranData = <?= json_encode($barPengeluaran) ?>;

        // Chart Saldo (jumlah transaksi per tanggal)
        new Chart(document.getElementById("barSaldoChart"), {
            type: 'bar',
            data: {
                labels: tanggal,
                datasets: [
                    { label: 'Pemasukan', data: transaksiPemasukan, backgroundColor: '#55efc4' },
                    { label: 'Pengeluaran', data: transaksiPengeluaran, backgroundColor: '#ff7675' }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { 
                    y: { beginAtZero: true, title: { display: true, text: 'Jumlah Transaksi' } },
                    x: { title: { display: true, text: 'Tanggal' } }
                }
            }
        });

        // Chart Total Pemasukan per Jenis
        const pemasukanLabels = pemasukanData.map(p => p.jenis);
        const pemasukanValues = pemasukanData.map(p => p.total);
        new Chart(document.getElementById("barPemasukanJenis"), {
            type: 'bar',
            data: {
                labels: pemasukanLabels,
                datasets: [{
                    label: 'Nominal Pemasukan (Rp)',
                    data: pemasukanValues,
                    backgroundColor: ['#55efc4','#00b894','#fd7e14','#6c5ce7','#fab1a0']
                }]
            },
            options: { responsive: true }
        });

        // Chart Total Pengeluaran per Jenis
        const pengeluaranLabels = pengeluaranData.map(p => p.jenis);
        const pengeluaranValues = pengeluaranData.map(p => p.total);
        new Chart(document.getElementById("barPengeluaranJenis"), {
            type: 'bar',
            data: {
                labels: pengeluaranLabels,
                datasets: [{
                    label: 'Nominal Pengeluaran (Rp)',
                    data: pengeluaranValues,
                    backgroundColor: ['#ff7675','#ffeaa7','#55efc4','#74b9ff','#dfe6e9']
                }]
            },
            options: { responsive: true }
        });

        // Tombol card klik untuk ganti chart
        document.querySelectorAll('.card-click').forEach(card => {
            card.addEventListener('click', () => {
                const targetId = card.dataset.chart;
                document.querySelectorAll('.chart-container').forEach(c => c.classList.remove('active'));
                document.getElementById(targetId).classList.add('active');
            });
        });
    </script>
</body>

</html>
