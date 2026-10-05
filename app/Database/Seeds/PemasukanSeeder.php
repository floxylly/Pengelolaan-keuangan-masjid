<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PemasukanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            
        ];

        $this->db->table('pemasukan')->insertBatch($data);
    }
}