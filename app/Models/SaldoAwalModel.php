<?php

namespace App\Models;

use CodeIgniter\Model;

class SaldoAwalModel extends Model
{
    protected $table = 'saldo_awal';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tanggal',
        'jumlah',
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';
}