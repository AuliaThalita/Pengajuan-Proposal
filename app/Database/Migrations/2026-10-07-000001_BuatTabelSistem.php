<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelSistem extends Migration
{
    public function up()
    {
        $id = ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true];
        $fk = ['type' => 'INT', 'constraint' => 10, 'unsigned' => true];

        // ---- ukm
        $this->forge->addField([
            'id'         => $id,
            'nama_ukm'   => ['type' => 'VARCHAR', 'constraint' => 150],
            'deskripsi'  => ['type' => 'TEXT', 'null' => true],
            'pembina_id' => $fk + ['null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ukm');

        // ---- users
        $this->forge->addField([
            'id'         => $id,
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'ENUM', 'constraint' => ['ukm', 'pembina', 'birmawa']],
            'ukm_id'     => $fk + ['null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('ukm_id', 'ukm', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('users');

        $this->db->query('ALTER TABLE ukm ADD CONSTRAINT fk_ukm_pembina FOREIGN KEY (pembina_id) REFERENCES users(id) ON DELETE SET NULL');

        // ---- proposals
        $this->forge->addField([
            'id'              => $id,
            'ukm_id'          => $fk,
            'diajukan_oleh'   => $fk + ['null' => true],
            'judul'           => ['type' => 'VARCHAR', 'constraint' => 200],
            'latar_belakang'  => ['type' => 'TEXT'],
            'tujuan'          => ['type' => 'TEXT'],
            'sasaran'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'tanggal_mulai'   => ['type' => 'DATE'],
            'tanggal_selesai' => ['type' => 'DATE'],
            'lokasi'          => ['type' => 'VARCHAR', 'constraint' => 200],
            'anggaran'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'          => ['type' => 'ENUM', 'constraint' => ['diajukan', 'disetujui_pembina', 'ditolak_pembina', 'divalidasi', 'ditolak_birmawa'], 'default' => 'diajukan'],
            'catatan_pembina' => ['type' => 'TEXT', 'null' => true],
            'catatan_birmawa' => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('ukm_id', 'ukm', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('diajukan_oleh', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('proposals');

        // ---- dokumen
        $this->forge->addField([
            'id'          => $id,
            'proposal_id' => $fk,
            'jenis'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'nama_asli'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama_file'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'ukuran'      => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('proposal_id', 'proposals', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumen');

        // ---- proposal_log
        $this->forge->addField([
            'id'          => $id,
            'proposal_id' => $fk,
            'user_id'     => $fk + ['null' => true],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 30],
            'catatan'     => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('proposal_id', 'proposals', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('proposal_log');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE ukm DROP FOREIGN KEY fk_ukm_pembina');
        foreach (['proposal_log', 'dokumen', 'proposals', 'users', 'ukm'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
