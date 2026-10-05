<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kas DKM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }

        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header h4 {
            margin: 0;
            font-weight: normal;
            font-size: 14px;
        }

        .saldo-awal,
        .saldo-akhir {
            background-color: #e0f2cc;
            font-weight: bold;
            padding: 6px 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .no-border {
            border: none;
        }

        .footer {
            margin-top: 30px;
        }

        .footer td {
            vertical-align: top;
            padding-top: 10px;
        }
    </style>
</head>

<body>

<?php
$tanggalAwal  = $tanggal_awal ?? null;
$tanggalAkhir = $tanggal_akhir ?? null;

$saldoAwal = (int) ($saldo_awal ?? 0);
$saldoAkhir = (int) ($saldo_akhir ?? 0);

$pemasukanData = $pemasukan ?? [];
$pengeluaranData = $pengeluaran_detail ?? [];

$totalPemasukan = (int) ($total_pemasukan ?? 0);
$totalPengeluaran = (int) ($total_pengeluaran ?? 0);
?>

<!-- HEADER -->
<div class="header">
    <h2>LAPORAN KAS DKM AL ANSHOR</h2>

    <?php if ($tanggalAwal && $tanggalAkhir): ?>
        <h4>
            Periode
            <?= esc(date('d/m/Y', strtotime($tanggalAwal))) ?>
            s.d.
            <?= esc(date('d/m/Y', strtotime($tanggalAkhir))) ?>
        </h4>
    <?php endif; ?>
</div>


<!-- SALDO AWAL -->
<div class="saldo">
    A. SALDO AWAL :
    Rp <?= number_format($saldoAwal, 0, ',', '.') ?>
</div>


<!-- PEMASUKAN -->
<div class="section-title">
    B. PEMASUKAN
</div>

<table class="data">
    <thead>
        <tr>
            <th width="15%">Tanggal</th>
            <th width="25%">Jenis</th>
            <th>Keterangan</th>
            <th width="25%">Jumlah</th>
        </tr>
    </thead>

    <tbody>

    <?php if (!empty($pemasukanData)): ?>

        <?php foreach ($pemasukanData as $p): ?>

            <tr>
                <td class="text-center">
                    <?= esc(date('d/m/Y', strtotime($p['tanggal']))) ?>
                </td>

                <td>
                    <?= esc($p['jenis'] ?? '-') ?>
                </td>

                <td>
                    <?= esc($p['keterangan'] ?? '-') ?>
                </td>

                <td class="text-right">
                    Rp <?= number_format((int) $p['jumlah'], 0, ',', '.') ?>
                </td>
            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="4" class="text-center">
                Tidak ada data pemasukan.
            </td>
        </tr>

    <?php endif; ?>


        <tr class="total-row">
            <td colspan="3">
                TOTAL PEMASUKAN
            </td>

            <td class="text-right">
                Rp <?= number_format($totalPemasukan, 0, ',', '.') ?>
            </td>
        </tr>

    </tbody>
</table>


<!-- PENGELUARAN -->
<div class="section-title">
    C. PENGELUARAN
</div>

<table class="data">
    <thead>
        <tr>
            <th width="15%">Tanggal</th>
            <th width="25%">Jenis</th>
            <th>Keterangan</th>
            <th width="25%">Jumlah</th>
        </tr>
    </thead>

    <tbody>

    <?php if (!empty($pengeluaranData)): ?>

        <?php foreach ($pengeluaranData as $p): ?>

            <tr>
                <td class="text-center">
                    <?= esc(date('d/m/Y', strtotime($p['tanggal']))) ?>
                </td>

                <td>
                    <?= esc($p['jenis'] ?? '-') ?>
                </td>

                <td>
                    <?= esc($p['keterangan'] ?? '-') ?>
                </td>

                <td class="text-right">
                    Rp <?= number_format((int) $p['jumlah'], 0, ',', '.') ?>
                </td>
            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="4" class="text-center">
                Tidak ada data pengeluaran.
            </td>
        </tr>

    <?php endif; ?>


        <tr class="total-row">
            <td colspan="3">
                TOTAL PENGELUARAN
            </td>

            <td class="text-right">
                Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?>
            </td>
        </tr>

    </tbody>
</table>


<!-- SALDO AKHIR -->
<div class="saldo">
    D. SALDO AKHIR (A + B - C) :
    Rp <?= number_format($saldoAkhir, 0, ',', '.') ?>
</div>


<!-- FOOTER -->
<table class="footer">

    <tr>
        <td colspan="2">
            <strong>Rekening Donasi:</strong><br>
            7138951523<br>
            BSI a.n. DKM AL ANSHOR
        </td>
    </tr>

    <tr>
        <td>
            <strong>Bendahara:</strong><br>
            Wahyu Eko Nugroho<br>
            HP. 082123400403
        </td>

        <td class="footer-right">
            <strong>Ketua:</strong><br>
            Asep Hidayat<br>
            HP. 081316695419
        </td>
    </tr>

</table>

</body>

</html>
