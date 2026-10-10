<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table         = 'proposal_log';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['proposal_id', 'user_id', 'status', 'catatan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function catat(int $proposalId, int $userId, string $status, ?string $catatan = null): void
    {
        $this->insert([
            'proposal_id' => $proposalId,
            'user_id'     => $userId,
            'status'      => $status,
            'catatan'     => $catatan,
        ]);
    }

    public function riwayat(int $proposalId): array
    {
        return $this->db->table('proposal_log l')
            ->select('l.*, u.nama, u.role')
            ->join('users u', 'u.id = l.user_id', 'left')
            ->where('l.proposal_id', $proposalId)
            ->orderBy('l.id', 'ASC')
            ->get()->getResultArray();
    }
}
