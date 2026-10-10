<?php

namespace App\Models;

use CodeIgniter\Model;

class UkmModel extends Model
{
    protected $table         = 'ukm';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_ukm', 'deskripsi', 'pembina_id'];
    protected $useTimestamps = true;

    public function daftarLengkap(): array
    {
        return $this->db->table('ukm k')
            ->select('k.*, u.nama AS nama_pembina')
            ->join('users u', 'u.id = k.pembina_id', 'left')
            ->orderBy('k.nama_ukm')
            ->get()->getResultArray();
    }
}
