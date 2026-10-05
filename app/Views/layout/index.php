<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Keuangan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f6f6e9;
        margin: 0;
        padding: 0;
        display: flex;
    }

    .sidebar {
        width: 240px;
        background-color: #7d9b76;
        height: 100vh;
        padding: 2em 1em;
        color: white;
        position: fixed;
    }

    .sidebar h2 {
        font-size: 1.4em;
        margin-bottom: 2em;
        text-align: center;
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
        padding: 0.6em 1em;
        border-radius: 10px;
        transition: background 0.3s;
    }

    .sidebar ul li a.active,
    .sidebar ul li a:hover {
        background-color: #cbddd1;
        color: black;
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
        flex: 1;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
        margin-bottom: 40px;
    }

    .card {
        flex: 1 1 calc(25% - 20px);
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        text-align: center;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .card h3 {
        margin-bottom: 10px;
        font-size: 1.1em;
        color: #555;
    }

    .card p {
        font-size: 1.3em;
        font-weight: bold;
        color: #333;
    }

    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        justify-content: space-around;
    }

    .chart-box {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    canvas {
        max-width: 500px;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Keuangan<br>Masjid Al-Anshor</h2>
        <ul>
            <li><a href="/dashboard" class="active">Dashboard</a></li>
            <li><a href="/pemasukan">Pemasukan</a></li>
            <li><a href="/pengeluaran">Pengeluaran</a></li>
            <li><a href="/logout">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Dashboard Keuangan Masjid</h2>

        <div class="card-container">
            <div class="card">
                <h3>Saldo Saat Ini</h3>
                <p>Rp <?= number_format($saldoSaatIni, 0, ',', '.') ?></p>
            </div>
            <div class="card">
                <h3>Total Pemasukan</h3>
                <p>Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></p>
            </div>
            <div class="card">
                <h3>Total Pengeluaran</h3>
                <p>Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></p>
            </div>
            <div class="card">
                <h3>Jumlah Transaksi</h3>
                <p><?= $jumlahTransaksi ?></p>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-box">
                <h4 style="text-align:center; margin-bottom:15px;">Pie Chart Pemasukan per Jenis</h4>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="chart-box">
                <h4 style="text-align:center; margin-bottom:15px;">Bar Chart Pengeluaran per Jenis</h4>
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    const pieData = {
        labels: <?= json_encode(array_column($piePemasukan, 'jenis')) ?>,
        datasets: [{
            label: 'Pemasukan',
            data: <?= json_encode(array_column($piePemasukan, 'total')) ?>,
            backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0']
        }]
    };

    const barData = {
        labels: <?= json_encode(array_column($barPengeluaran, 'jenis')) ?>,
        datasets: [{
            label: 'Pengeluaran',
            data: <?= json_encode(array_column($barPengeluaran, 'total')) ?>,
            backgroundColor: '#FF6384'
        }]
    };

    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: pieData
    });

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: barData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>
</body>

</html>