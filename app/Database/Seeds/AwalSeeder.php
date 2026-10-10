<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/** php spark db:seed AwalSeeder  (akun awal; password semua: Password123) */
class AwalSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $pw  = password_hash('Password123', PASSWORD_DEFAULT);

        $this->db->table('ukm')->insertBatch([
            ['nama_ukm' => 'UKM Robotika', 'deskripsi' => 'Pengembangan minat robotika dan teknologi.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_ukm' => 'UKM Basket',   'deskripsi' => 'Pembinaan olahraga bola basket.',            'created_at' => $now, 'updated_at' => $now],
        ]);
        $ukm = $this->db->table('ukm')->select('id')->orderBy('id')->get()->getResultArray();

        $u = $this->db->table('users');
        $u->insert(['nama' => 'Admin Birmawa',    'email' => 'birmawa@mercubuana.ac.id',  'password' => $pw, 'role' => 'birmawa', 'created_at' => $now, 'updated_at' => $now]);
        $u->insert(['nama' => 'Dr. Budi Santoso', 'email' => 'pembina1@mercubuana.ac.id', 'password' => $pw, 'role' => 'pembina', 'created_at' => $now, 'updated_at' => $now]);
        $pembinaId = $this->db->insertID();
        $u->insert(['nama' => 'Ketua UKM Robotika', 'email' => 'robotika@mercubuana.ac.id', 'password' => $pw, 'role' => 'ukm', 'ukm_id' => $ukm[0]['id'], 'created_at' => $now, 'updated_at' => $now]);
        $u->insert(['nama' => 'Ketua UKM Basket',   'email' => 'basket@mercubuana.ac.id',   'password' => $pw, 'role' => 'ukm', 'ukm_id' => $ukm[1]['id'], 'created_at' => $now, 'updated_at' => $now]);

        $this->db->table('ukm')->update(['pembina_id' => $pembinaId]);
    }
}
