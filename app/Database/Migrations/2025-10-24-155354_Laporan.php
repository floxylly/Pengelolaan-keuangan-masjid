<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Laporan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tanggal_awal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_akhir' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'saldo_awal' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'default' => 0,
            ],
            'total_pemasukan' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'default' => 0,
            ],
            'total_pengeluaran' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'default' => 0,
            ],
            'saldo_akhir' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'default' => 0,
            ],
            'nama_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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

        $this->forge->addUniqueKey([
            'tanggal_awal',
            'tanggal_akhir'
        ]);

        $this->forge->createTable('laporan', true);
    }

    public function down()
    {
        $this->forge->dropTable('laporan', true);
    }
}