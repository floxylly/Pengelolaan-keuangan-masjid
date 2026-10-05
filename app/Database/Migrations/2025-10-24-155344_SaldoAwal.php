<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SaldoAwal extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'jumlah' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('saldo_awal', true);
    }

    public function down()
    {
        $this->forge->dropTable('saldo_awal', true);
    }
}