<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tanggal_awal',
        'tanggal_akhir',
        'saldo_awal',
        'total_pemasukan',
        'total_pengeluaran',
        'saldo_akhir',
        'nama_file',
    ];

    protected $useTimestamps = true;

    public function cekDuplikat($awal, $akhir)
    {
        return $this->where('tanggal_awal', $awal)
                    ->where('tanggal_akhir', $akhir)
                    ->first();
    }
}