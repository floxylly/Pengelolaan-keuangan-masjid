<?php

namespace App\Controllers;

use App\Models\PemasukanModel;
use App\Models\PengeluaranDetailModel;
use App\Models\SaldoAwalModel;
use App\Models\LaporanModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Laporan extends BaseController
{
    protected $pemasukanModel;
    protected $pengeluaranDetailModel;
    protected $saldoAwalModel;
    protected $laporanModel;

    protected $allowedRoles = [
        'bendahara_umum',
        'sekretaris'
    ];

    public function __construct()
    {
        $this->pemasukanModel = new PemasukanModel();
        $this->pengeluaranDetailModel = new PengeluaranDetailModel();
        $this->saldoAwalModel = new SaldoAwalModel();
        $this->laporanModel = new LaporanModel();
    }

    private function checkLogin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return null;
    }

    private function checkRole()
    {
        $role = session()->get('role');

        if (!in_array($role, $this->allowedRoles, true)) {
            return redirect()->to('/dashboard')
                ->with('error', 'Anda tidak memiliki akses.');
        }

        return null;
    }

    /**
     * Mengambil saldo awal yang berlaku untuk suatu periode.
     */
    private function getSaldoAwalData($tanggalAwal)
    {
        return $this->saldoAwalModel
            ->where('tanggal <=', $tanggalAwal)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Menghitung saldo awal periode.
     *
     * Rumus:
     * saldo awal sistem
     * + pemasukan sebelum periode
     * - pengeluaran sebelum periode
     */
    private function hitungSaldoAwalPeriode($tanggalAwal)
    {
        $saldoData = $this->getSaldoAwalData($tanggalAwal);

        $saldoAwalSistem = $saldoData
            ? (int) $saldoData['jumlah']
            : 0;

        /*
         * Jika ada saldo awal yang berlaku,
         * transaksi dihitung setelah tanggal saldo awal tersebut.
         */
        $incomeQuery = $this->pemasukanModel
            ->selectSum('jumlah', 'total')
            ->where('tanggal <', $tanggalAwal);

        $expenseQuery = $this->pengeluaranDetailModel
            ->selectSum('pengeluaran_detail.jumlah', 'total')
            ->join(
                'pengeluaran',
                'pengeluaran.id = pengeluaran_detail.pengeluaran_id',
                'inner'
            )
            ->where('pengeluaran.tanggal <', $tanggalAwal);

        if ($saldoData) {

            $incomeQuery
                ->where('tanggal >', $saldoData['tanggal']);

            $expenseQuery
                ->where(
                    'pengeluaran.tanggal >',
                    $saldoData['tanggal']
                );
        }

        $totalPemasukanSebelum = (int) (
            $incomeQuery->first()['total'] ?? 0
        );

        $totalPengeluaranSebelum = (int) (
            $expenseQuery->first()['total'] ?? 0
        );

        return $saldoAwalSistem
            + $totalPemasukanSebelum
            - $totalPengeluaranSebelum;
    }

    public function index()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $laporan = $this->laporanModel
            ->orderBy('tanggal_awal', 'DESC')
            ->findAll();

        return view('laporan/index', [
            'laporan' => $laporan,
        ]);
    }

    public function buat()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        $tanggalAwal = $this->request->getPost('tanggal_awal');
        $tanggalAkhir = $this->request->getPost('tanggal_akhir');

        if (!$tanggalAwal || !$tanggalAkhir) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal awal dan tanggal akhir wajib diisi.');
        }

        if ($tanggalAwal > $tanggalAkhir) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Tanggal awal tidak boleh lebih besar dari tanggal akhir.'
                );
        }

        /*
         * Cegah laporan periode yang sama dibuat dua kali.
         */
        $duplicate = $this->laporanModel
            ->cekDuplikat($tanggalAwal, $tanggalAkhir);

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Laporan untuk periode tersebut sudah ada.'
                );
        }

        /*
         * ============================
         * SALDO AWAL
         * ============================
         */
        $saldoAwal = $this->hitungSaldoAwalPeriode($tanggalAwal);

        /*
         * ============================
         * PEMASUKAN PERIODE
         * ============================
         */
        $pemasukan = $this->pemasukanModel
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $totalPemasukan = 0;

        foreach ($pemasukan as $row) {
            $totalPemasukan += (int) $row['jumlah'];
        }

        /*
         * Rekap pemasukan berdasarkan jenis.
         */
        $pemasukanPerJenis = [];

        foreach ($pemasukan as $row) {

            $jenis = $row['jenis'];

            if (!isset($pemasukanPerJenis[$jenis])) {
                $pemasukanPerJenis[$jenis] = 0;
            }

            $pemasukanPerJenis[$jenis] += (int) $row['jumlah'];
        }

        /*
         * ============================
         * PENGELUARAN PERIODE
         * ============================
         */
        $pengeluaranDetail = $this->pengeluaranDetailModel
            ->select('pengeluaran_detail.*')
            ->join(
                'pengeluaran',
                'pengeluaran.id = pengeluaran_detail.pengeluaran_id',
                'inner'
            )
            ->where('pengeluaran.tanggal >=', $tanggalAwal)
            ->where('pengeluaran.tanggal <=', $tanggalAkhir)
            ->orderBy('pengeluaran.tanggal', 'ASC')
            ->orderBy('pengeluaran_detail.id', 'ASC')
            ->findAll();

        $totalPengeluaran = 0;

        foreach ($pengeluaranDetail as $row) {
            $totalPengeluaran += (int) $row['jumlah'];
        }

        /*
         * Rekap pengeluaran berdasarkan jenis.
         */
        $pengeluaranPerJenis = [];

        foreach ($pengeluaranDetail as $row) {

            $jenis = $row['jenis'];

            if (!isset($pengeluaranPerJenis[$jenis])) {
                $pengeluaranPerJenis[$jenis] = 0;
            }

            $pengeluaranPerJenis[$jenis] += (int) $row['jumlah'];
        }

        /*
         * ============================
         * SALDO AKHIR
         * ============================
         */
        $saldoAkhir =
            $saldoAwal
            + $totalPemasukan
            - $totalPengeluaran;

        /*
         * ============================
         * DATA PDF
         * ============================
         */
        $data = [
            'tanggal_awal'       => $tanggalAwal,
            'tanggal_akhir'      => $tanggalAkhir,
            'saldo_awal'         => $saldoAwal,
            'pemasukan'          => $pemasukan,
            'pengeluaran_detail' => $pengeluaranDetail,
            'total_pemasukan'    => $totalPemasukan,
            'total_pengeluaran'  => $totalPengeluaran,
            'saldo_akhir'        => $saldoAkhir,
            'pemasukan_per_jenis'   => $pemasukanPerJenis,
            'pengeluaran_per_jenis' => $pengeluaranPerJenis,
        ];

        /*
         * ============================
         * GENERATE PDF
         * ============================
         */
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        /*
         * Pastikan view ini memang ada:
         * app/Views/laporan/pdf.php
         */
        $html = view('laporan/pdf', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        /*
         * Nama file PDF.
         */
        $namaFile =
            'laporan_' .
            $tanggalAwal .
            '_' .
            $tanggalAkhir .
            '.pdf';

        $folder = WRITEPATH . 'uploads/laporan/';

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        file_put_contents(
            $folder . $namaFile,
            $dompdf->output()
        );

        /*
         * ============================
         * SIMPAN SNAPSHOT LAPORAN
         * ============================
         */
        $this->laporanModel->insert([
            'tanggal_awal'      => $tanggalAwal,
            'tanggal_akhir'     => $tanggalAkhir,
            'saldo_awal'        => $saldoAwal,
            'total_pemasukan'   => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo_akhir'       => $saldoAkhir,
            'nama_file'         => $namaFile,
        ]);

        return redirect()->to('/laporan')
            ->with(
                'success',
                'Laporan berhasil dibuat dan disimpan.'
            );
    }

    public function download($namaFile)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        /*
         * Lindungi dari path traversal.
         */
        $namaFile = basename($namaFile);

        $path = WRITEPATH . 'uploads/laporan/' . $namaFile;

        if (!is_file($path)) {
            return redirect()->to('/laporan')
                ->with('error', 'File laporan tidak ditemukan.');
        }

        return $this->response->download(
            $path,
            null
        );
    }

    public function hapus($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        $laporan = $this->laporanModel->find($id);

        if (!$laporan) {
            return redirect()->to('/laporan')
                ->with('error', 'Data laporan tidak ditemukan.');
        }

        /*
         * Hapus file PDF jika masih ada.
         */
        if (!empty($laporan['nama_file'])) {

            $path =
                WRITEPATH .
                'uploads/laporan/' .
                basename($laporan['nama_file']);

            if (is_file($path)) {
                unlink($path);
            }
        }

        /*
         * Hapus data laporan.
         */
        $this->laporanModel->delete($id);

        return redirect()->to('/laporan')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}