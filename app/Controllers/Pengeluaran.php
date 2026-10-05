<?php

namespace App\Controllers;

use App\Models\PengeluaranModel;
use App\Models\PengeluaranDetailModel;

class Pengeluaran extends BaseController
{
    protected $pengeluaranModel;
    protected $pengeluaranDetailModel;

    protected $bendaharaRoles = [
        'bendahara_umum',
        'sekretaris'
    ];

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
        $this->pengeluaranDetailModel = new PengeluaranDetailModel();
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

    /**
     * Bersihkan format rupiah.
     *
     * Contoh:
     * 500.000 -> 500000
     * 1.500.000 -> 1500000
     */
    private function parseJumlah($jumlah)
    {
        if ($jumlah === null || $jumlah === '') {
            return 0;
        }

        $jumlah = trim((string) $jumlah);

        // Hilangkan titik pemisah ribuan
        $jumlah = str_replace('.', '', $jumlah);

        // Hilangkan koma jika ada
        $jumlah = str_replace(',', '', $jumlah);

        return (int) $jumlah;
    }

    public function index()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $pengeluaran = $this->pengeluaranModel
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];
        $totalPengeluaran = 0;

        foreach ($pengeluaran as $row) {

            $detail = $this->pengeluaranDetailModel
                ->where('pengeluaran_id', $row['id'])
                ->orderBy('id', 'ASC')
                ->findAll();

            $total = 0;
            $jumlahJenis = 0;

            foreach ($detail as $item) {
                $total += (int) $item['jumlah'];
                $jumlahJenis++;
            }

            $row['detail'] = $detail;
            $row['total'] = $total;
            $row['jumlah_jenis'] = $jumlahJenis;

            $data[] = $row;

            $totalPengeluaran += $total;
        }

        return view('pengeluaran/index', [
            'pengeluaran'      => $data,
            'totalSaldo'       => $totalPengeluaran,
            'totalPengeluaran' => $totalPengeluaran,
            'role'             => session()->get('role'),
            'bendaharaRoles'   => $this->bendaharaRoles,
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
                ->with(
                    'error',
                    'Tanggal pengeluaran wajib diisi.'
                );
        }

        if (empty($jenis) || empty($jumlah)) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Minimal satu detail pengeluaran wajib diisi.'
                );
        }

        $existing = $this->pengeluaranModel
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Data pengeluaran untuk tanggal tersebut sudah ada.'
                );
        }

        $db = \Config\Database::connect();

        $db->transStart();

        $pengeluaranId = $this->pengeluaranModel->insert([
            'tanggal' => $tanggal,
            'user_id' => session()->get('user_id'),
        ], true);

        if (!$pengeluaranId) {
            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal menyimpan data pengeluaran.'
                );
        }

        $jumlahDetailBerhasil = 0;

        foreach ($jenis as $key => $jenisItem) {

            $jenisItem = trim((string) $jenisItem);

            if ($jenisItem === '') {
                continue;
            }

            $jumlahItem = $jumlah[$key] ?? '';
            $jumlahBersih = $this->parseJumlah($jumlahItem);

            if ($jumlahBersih <= 0) {
                continue;
            }

            $keteranganItem = $keterangan[$key] ?? null;

            $this->pengeluaranDetailModel->insert([
                'pengeluaran_id' => $pengeluaranId,
                'tanggal'        => $tanggal,
                'jenis'          => $jenisItem,
                'jumlah'         => $jumlahBersih,
                'keterangan'     => $keteranganItem ?: null,
            ]);

            $jumlahDetailBerhasil++;
        }

        if ($jumlahDetailBerhasil === 0) {
            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Minimal satu detail pengeluaran dengan jumlah yang valid harus diisi.'
                );
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal menyimpan data pengeluaran.'
                );
        }

        return redirect()->to('/pengeluaran')
            ->with(
                'success',
                'Data pengeluaran berhasil ditambahkan.'
            );
    }

    public function update($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        $existing = $this->pengeluaranModel->find($id);

        if (!$existing) {
            return redirect()->to('/pengeluaran')
                ->with(
                    'error',
                    'Data pengeluaran tidak ditemukan.'
                );
        }

        $tanggal = $this->request->getPost('tanggal');

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

        if (!$tanggal) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Tanggal pengeluaran wajib diisi.'
                );
        }

        if (empty($jenis) || empty($jumlah)) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Minimal satu detail pengeluaran wajib diisi.'
                );
        }

        $duplicate = $this->pengeluaranModel
            ->where('tanggal', $tanggal)
            ->where('id !=', $id)
            ->first();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Tanggal tersebut sudah digunakan oleh pengeluaran lain.'
                );
        }

        $db = \Config\Database::connect();

        $db->transStart();

        $this->pengeluaranModel->update($id, [
            'tanggal' => $tanggal,
        ]);

        $this->pengeluaranDetailModel
            ->where('pengeluaran_id', $id)
            ->delete();

        $jumlahDetailBerhasil = 0;

        foreach ($jenis as $key => $jenisItem) {

            $jenisItem = trim((string) $jenisItem);

            if ($jenisItem === '') {
                continue;
            }

            $jumlahItem = $jumlah[$key] ?? '';
            $jumlahBersih = $this->parseJumlah($jumlahItem);

            if ($jumlahBersih <= 0) {
                continue;
            }

            $keteranganItem = $keterangan[$key] ?? null;

            $this->pengeluaranDetailModel->insert([
                'pengeluaran_id' => $id,
                'tanggal'        => $tanggal,
                'jenis'          => $jenisItem,
                'jumlah'         => $jumlahBersih,
                'keterangan'     => $keteranganItem ?: null,
            ]);

            $jumlahDetailBerhasil++;
        }

        if ($jumlahDetailBerhasil === 0) {
            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Minimal satu detail pengeluaran dengan jumlah yang valid harus diisi.'
                );
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memperbarui data pengeluaran.'
                );
        }

        return redirect()->to('/pengeluaran')
            ->with(
                'success',
                'Data pengeluaran berhasil diperbarui.'
            );
    }

    public function detail($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Belum login.',
                ]);
        }

        $pengeluaran = $this->pengeluaranModel->find($id);

        if (!$pengeluaran) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan.',
                ]);
        }

        $detail = $this->pengeluaranDetailModel
            ->where('pengeluaran_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        /*
         * View JavaScript membutuhkan array detail
         * secara langsung.
         */
        return $this->response->setJSON($detail);
    }

    public function delete($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($redirect = $this->checkRole()) {
            return $redirect;
        }

        $existing = $this->pengeluaranModel->find($id);

        if (!$existing) {
            return redirect()->to('/pengeluaran')
                ->with(
                    'error',
                    'Data pengeluaran tidak ditemukan.'
                );
        }

        /*
         * pengeluaran_detail akan ikut terhapus karena
         * foreign key menggunakan ON DELETE CASCADE.
         */
        $this->pengeluaranModel->delete($id);

        return redirect()->to('/pengeluaran')
            ->with(
                'success',
                'Data pengeluaran berhasil dihapus.'
            );
    }
}