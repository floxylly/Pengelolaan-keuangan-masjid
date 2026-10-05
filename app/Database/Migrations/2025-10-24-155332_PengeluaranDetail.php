<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PengeluaranDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pengeluaran_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'jumlah' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
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

        $this->forge->addForeignKey(
            'pengeluaran_id',
            'pengeluaran',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('pengeluaran_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('pengeluaran_detail', true);
    }
}