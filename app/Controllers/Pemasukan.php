<?php

namespace App\Controllers;

use App\Models\PemasukanModel;
use App\Models\PengeluaranDetailModel;
use App\Models\SaldoAwalModel;

class Pemasukan extends BaseController
{
    protected $pemasukanModel;
    protected $pengeluaranDetailModel;
    protected $saldoAwalModel;

    protected $bendaharaRoles = [
        'bendahara_umum',
        'sekretaris'
    ];

    public function __construct()
    {
        $this->pemasukanModel = new PemasukanModel();
        $this->pengeluaranDetailModel = new PengeluaranDetailModel();
        $this->saldoAwalModel = new SaldoAwalModel();
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

        if (!in_array($role, $this->bendaharaRoles, true)) {
            return redirect()->to('/dashboard')
                ->with('error', 'Anda tidak memiliki akses.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        /*
         * =========================================================
         * DATA PEMASUKAN
         * =========================================================
         */
        $dataPemasukan = $this->pemasukanModel
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        /*
         * =========================================================
         * REKAP PEMASUKAN BERDASARKAN TANGGAL
         * =========================================================
         */
        $rekap = [];

        foreach ($dataPemasukan as $row) {
            $tanggal = $row['tanggal'];

            if (!isset($rekap[$tanggal])) {
                $rekap[$tanggal] = [
                    'tanggal'      => $tanggal,
                    'jumlah_jenis' => 0,
                    'total'        => 0,
                ];
            }

            $rekap[$tanggal]['jumlah_jenis']++;
            $rekap[$tanggal]['total'] += (int) $row['jumlah'];
        }

        /*
         * =========================================================
         * TOTAL SALDO
         *
         * Saldo = saldo awal + seluruh pemasukan - seluruh
         * pengeluaran.
         * =========================================================
         */
        $tanggalSekarang = date('Y-m-d');

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
         * Total pemasukan setelah saldo awal.
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
         * Total pengeluaran setelah saldo awal.
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
         * Saldo saat ini.
         */
        $totalSaldo = $saldoAwal
            + $totalPemasukan
            - $totalPengeluaran;

        /*
         * =========================================================
         * DATA UNTUK VIEW
         * =========================================================
         */
        $role = session()->get('role');

        return view('pemasukan/index', [
            'pemasukan'     => array_values($rekap),
            'totalSaldo'    => $totalSaldo,
            'role'          => $role,
            'bendaharaRoles'=> $this->bendaharaRoles,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        $tanggal = $this->request->getPost('tanggal');

        $jenis = $this->request->getPost('jenis');
        $jumlah = $this->request->getPost('jumlah');
        $keterangan = $this->request->getPost('keterangan');

        /*
         * Pastikan data berbentuk array.
         */
        if (!is_array($jenis)) {
            $jenis = [$jenis];
        }

        if (!is_array($jumlah)) {
            $jumlah = [$jumlah];
        }

        if (!is_array($keterangan)) {
            $keterangan = [$keterangan];
        }

        if (!$tanggal) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal wajib diisi.');
        }

        /*
         * Satu tanggal hanya boleh memiliki satu kelompok
         * pemasukan.
         */
        $existing = $this->pemasukanModel
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Data pemasukan untuk tanggal tersebut sudah ada.'
                );
        }

        /*
         * Pastikan minimal ada satu data pemasukan.
         */
        if (empty($jenis) || empty($jumlah)) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Minimal satu data pemasukan harus diisi.'
                );
        }

        /*
         * Simpan seluruh baris pemasukan.
         */
        foreach ($jenis as $index => $jenisPemasukan) {

            if (
                empty($jenisPemasukan) ||
                !isset($jumlah[$index]) ||
                $jumlah[$index] === ''
            ) {
                continue;
            }

            /*
             * Bersihkan format Rupiah.
             * Contoh:
             * 500.000 → 500000
             */
            $jumlahBersih = str_replace('.', '', $jumlah[$index]);
            $jumlahBersih = str_replace(',', '', $jumlahBersih);

            $this->pemasukanModel->insert([
                'tanggal'    => $tanggal,
                'jenis'      => trim($jenisPemasukan),
                'sub_jenis'  => null,
                'jumlah'     => (int) $jumlahBersih,
                'keterangan' => $keterangan[$index] ?? null,
                'user_id'    => session()->get('user_id'),
            ]);
        }

        return redirect()->to('/pemasukan')
            ->with(
                'success',
                'Data pemasukan berhasil ditambahkan.'
            );
    }

    public function update($tanggal)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        /*
         * Cari seluruh data pada tanggal tersebut.
         */
        $dataLama = $this->pemasukanModel
            ->where('tanggal', $tanggal)
            ->findAll();

        if (!$dataLama) {
            return redirect()->to('/pemasukan')
                ->with(
                    'error',
                    'Data pemasukan tidak ditemukan.'
                );
        }

        $tanggalBaru = $this->request->getPost('tanggal');

        $jenis = $this->request->getPost('jenis');
        $jumlah = $this->request->getPost('jumlah');
        $keterangan = $this->request->getPost('keterangan');

        if (!is_array($jenis)) {
            $jenis = [$jenis];
        }

        if (!is_array($jumlah)) {
            $jumlah = [$jumlah];
        }

        if (!is_array($keterangan)) {
            $keterangan = [$keterangan];
        }

        if (!$tanggalBaru) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal wajib diisi.');
        }

        /*
         * Jika tanggal diubah, pastikan tidak bentrok
         * dengan tanggal lain.
         */
        if ($tanggalBaru !== $tanggal) {
            $duplicate = $this->pemasukanModel
                ->where('tanggal', $tanggalBaru)
                ->first();

            if ($duplicate) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Tanggal tersebut sudah digunakan oleh data pemasukan lain.'
                    );
            }
        }

        /*
         * Hapus seluruh data lama pada tanggal tersebut.
         */
        $this->pemasukanModel
            ->where('tanggal', $tanggal)
            ->delete();

        /*
         * Masukkan data hasil edit.
         */
        foreach ($jenis as $index => $jenisPemasukan) {

            if (
                empty($jenisPemasukan) ||
                !isset($jumlah[$index]) ||
                $jumlah[$index] === ''
            ) {
                continue;
            }

            $jumlahBersih = str_replace('.', '', $jumlah[$index]);
            $jumlahBersih = str_replace(',', '', $jumlahBersih);

            $this->pemasukanModel->insert([
                'tanggal'    => $tanggalBaru,
                'jenis'      => trim($jenisPemasukan),
                'sub_jenis'  => null,
                'jumlah'     => (int) $jumlahBersih,
                'keterangan' => $keterangan[$index] ?? null,
                'user_id'    => session()->get('user_id'),
            ]);
        }

        return redirect()->to('/pemasukan')
            ->with(
                'success',
                'Data pemasukan berhasil diperbarui.'
            );
    }

    public function delete($tanggal)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        $data = $this->pemasukanModel
            ->where('tanggal', $tanggal)
            ->findAll();

        if (!$data) {
            return redirect()->to('/pemasukan')
                ->with(
                    'error',
                    'Data pemasukan tidak ditemukan.'
                );
        }

        $this->pemasukanModel
            ->where('tanggal', $tanggal)
            ->delete();

        return redirect()->to('/pemasukan')
            ->with(
                'success',
                'Data pemasukan berhasil dihapus.'
            );
    }

    public function detail($tanggal)
    {
        if (!session()->get('logged_in')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Belum login.',
                ]);
        }

        $data = $this->pemasukanModel
            ->where('tanggal', $tanggal)
            ->orderBy('id', 'ASC')
            ->findAll();

        return $this->response->setJSON($data);
    }
}