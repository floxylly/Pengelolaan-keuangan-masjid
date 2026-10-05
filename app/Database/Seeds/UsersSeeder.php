<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'bendahara',
                'password' => password_hash('bendahara123', PASSWORD_DEFAULT),
                'role'     => 'bendahara_umum',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'sekretaris',
                'password' => password_hash('sekretaris987', PASSWORD_DEFAULT),
                'role'     => 'sekretaris',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
