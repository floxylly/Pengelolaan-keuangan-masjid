<?php

namespace App\Models;

use CodeIgniter\Model;

class PemasukanModel extends Model
{
    protected $table = 'pemasukan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tanggal',
        'jenis',
        'sub_jenis',
        'jumlah',
        'keterangan',
        'user_id',
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';
}