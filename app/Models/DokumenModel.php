<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table         = 'dokumen';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['proposal_id', 'jenis', 'nama_asli', 'nama_file', 'ukuran'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public const JENIS = [
        'proposal'        => 'Proposal Kegiatan',
        'surat_pengantar' => 'Surat Pengantar',
        'rab'             => 'Rencana Anggaran Biaya (RAB)',
        'panitia'         => 'Susunan Kepanitiaan (opsional)',
    ];

    public const WAJIB = ['proposal', 'surat_pengantar', 'rab'];
}
