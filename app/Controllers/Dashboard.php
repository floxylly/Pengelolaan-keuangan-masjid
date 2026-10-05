<?php

namespace App\Controllers;

use App\Models\PemasukanModel;
use App\Models\PengeluaranDetailModel;
use App\Models\SaldoAwalModel;

class Dashboard extends BaseController
{
    protected $pemasukanModel;
    protected $pengeluaranDetailModel;
    protected $saldoAwalModel;

    public function __construct()
    {
        $this->pemasukanModel = new PemasukanModel();
        $this->pengeluaranDetailModel = new PengeluaranDetailModel();
        $this->saldoAwalModel = new SaldoAwalModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $tanggalSekarang = date('Y-m-d');

        /*
         * =========================================================
         * SALDO AWAL
         * =========================================================
         */
        $saldoAwalData = $this->saldoAwalModel
            ->where('tanggal <=', $tanggalSekarang)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        $saldoAwal = $saldoAwalData
            ? (int) $saldoAwalData['jumlah']
            : 0;

        $tanggalMulaiTransaksi = $saldoAwalData
            ? $saldoAwalData['tanggal']
            : null;

        /*
         * =========================================================
         * TOTAL PEMASUKAN
         * =========================================================
         */
        $incomeQuery = clone $this->pemasukanModel;

        if ($tanggalMulaiTransaksi) {
            $incomeQuery->where('tanggal >', $tanggalMulaiTransaksi);
        }

        $incomeResult = $incomeQuery
            ->selectSum('jumlah', 'total')
            ->first();

        $totalPemasukan = (int) ($incomeResult['total'] ?? 0);

        /*
         * =========================================================
         * TOTAL PENGELUARAN
         * =========================================================
         */
        $expenseQuery = $this->pengeluaranDetailModel
            ->selectSum('pengeluaran_detail.jumlah', 'total')
            ->join(
                'pengeluaran',
                'pengeluaran.id = pengeluaran_detail.pengeluaran_id',
                'inner'
            );

        if ($tanggalMulaiTransaksi) {
            $expenseQuery->where(
                'pengeluaran.tanggal >',
                $tanggalMulaiTransaksi
            );
        }

        $expenseResult = $expenseQuery->first();

        $totalPengeluaran = (int) ($expenseResult['total'] ?? 0);

        /*
         * =========================================================
         * SALDO SAAT INI
         * =========================================================
         */
        $saldoSaatIni = $saldoAwal
            + $totalPemasukan
            - $totalPengeluaran;

        /*
         * =========================================================
         * JUMLAH TRANSAKSI
         * =========================================================
         */
        $jumlahTransaksiPemasukan = $this->pemasukanModel
            ->countAllResults();

        $jumlahTransaksiPengeluaran = $this->pengeluaranDetailModel
            ->countAllResults();

        $jumlahTransaksi = $jumlahTransaksiPemasukan
            + $jumlahTransaksiPengeluaran;

        /*
         * =========================================================
         * DATA GRAFIK BERDASARKAN TANGGAL
         * =========================================================
         */

        $tanggalPemasukan = $this->pemasukanModel
            ->select('tanggal')
            ->distinct()
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        $tanggalPengeluaran = $this->pengeluaranDetailModel
            ->select('pengeluaran.tanggal')
            ->join(
                'pengeluaran',
                'pengeluaran.id = pengeluaran_detail.pengeluaran_id',
                'inner'
            )
            ->distinct()
            ->orderBy('pengeluaran.tanggal', 'ASC')
            ->findAll();

        /*
         * Gabungkan semua tanggal transaksi.
         */
        $tanggalArray = [];

        foreach ($tanggalPemasukan as $row) {
            if (!empty($row['tanggal'])) {
                $tanggalArray[] = $row['tanggal'];
            }
        }

        foreach ($tanggalPengeluaran as $row) {
            if (!empty($row['tanggal'])) {
                $tanggalArray[] = $row['tanggal'];
            }
        }

        $tanggalTransaksi = array_values(
            array_unique($tanggalArray)
        );

        sort($tanggalTransaksi);

        /*
         * =========================================================
         * JUMLAH TRANSAKSI PEMASUKAN PER TANGGAL
         * =========================================================
         */
        $jumlahTransaksiPemasukan = [];

        /*
         * =========================================================
         * JUMLAH TRANSAKSI PENGELUARAN PER TANGGAL
         * =========================================================
         */
        $jumlahTransaksiPengeluaran = [];

        foreach ($tanggalTransaksi as $tanggal) {
            $jumlahPemasukan = $this->pemasukanModel
                ->where('tanggal', $tanggal)
                ->countAllResults();

            $jumlahPengeluaran = $this->pengeluaranDetailModel
                ->join(
                    'pengeluaran',
                    'pengeluaran.id = pengeluaran_detail.pengeluaran_id',
                    'inner'
                )
                ->where('pengeluaran.tanggal', $tanggal)
                ->countAllResults();

            $jumlahTransaksiPemasukan[] = $jumlahPemasukan;
            $jumlahTransaksiPengeluaran[] = $jumlahPengeluaran;
        }

        /*
         * =========================================================
         * GRAFIK PEMASUKAN BERDASARKAN JENIS
         * =========================================================
         */
        $piePemasukan = $this->pemasukanModel
            ->select('jenis, SUM(jumlah) AS total')
            ->groupBy('jenis')
            ->orderBy('total', 'DESC')
            ->findAll();

        /*
         * =========================================================
         * GRAFIK PENGELUARAN BERDASARKAN JENIS
         * =========================================================
         */
        $barPengeluaran = $this->pengeluaranDetailModel
            ->select('jenis, SUM(jumlah) AS total')
            ->groupBy('jenis')
            ->orderBy('total', 'DESC')
            ->findAll();

        /*
         * =========================================================
         * KIRIM DATA KE VIEW
         * =========================================================
         */
        return view('dashboard/index', [
            'saldoAwal'                 => $saldoAwal,
            'saldoSaatIni'              => $saldoSaatIni,
            'totalPemasukan'            => $totalPemasukan,
            'totalPengeluaran'          => $totalPengeluaran,

            'jumlahTransaksi'           => $jumlahTransaksi,
            'jumlahTransaksiPemasukan'  => $jumlahTransaksiPemasukan,
            'jumlahTransaksiPengeluaran'=> $jumlahTransaksiPengeluaran,

            'tanggalTransaksi'          => $tanggalTransaksi,

            'piePemasukan'              => $piePemasukan,
            'barPengeluaran'            => $barPengeluaran,
        ]);
    }
}