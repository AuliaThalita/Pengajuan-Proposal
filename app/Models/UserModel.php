<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama', 'email', 'password', 'role', 'ukm_id'];
    protected $useTimestamps    = true;

    public function daftarLengkap(): array
    {
        return $this->db->table('users u')
            ->select('u.*, k.nama_ukm')
            ->join('ukm k', 'k.id = u.ukm_id', 'left')
            ->orderBy('u.role')->orderBy('u.nama')
            ->get()->getResultArray();
    }
}
