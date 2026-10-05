<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranDetailModel extends Model
{
    protected $table = 'pengeluaran_detail';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'pengeluaran_id',
        'tanggal',
        'jenis',
        'jumlah',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';
}