<?php

namespace App\Models;

use CodeIgniter\Model;

class ProposalModel extends Model
{
    protected $table         = 'proposals';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'ukm_id', 'diajukan_oleh', 'judul', 'latar_belakang', 'tujuan', 'sasaran',
        'tanggal_mulai', 'tanggal_selesai', 'lokasi', 'anggaran', 'status',
        'catatan_pembina', 'catatan_birmawa',
    ];

    /**
     * Query dasar yang sudah dibatasi sesuai hak akses user:
     * - ukm     : hanya proposal UKM-nya sendiri
     * - pembina : hanya proposal dari UKM yang dibinanya
     * - birmawa : semua proposal
     */
    public function scope(array $user, bool $denganKolom = true)
    {
        $b = $this->db->table('proposals p');
        if ($denganKolom) {
            $b->select('p.*, k.nama_ukm, k.pembina_id');
        }
        $b->join('ukm k', 'k.id = p.ukm_id');

        if ($user['role'] === 'ukm') {
            $b->where('p.ukm_id', (int) $user['ukm_id']);
        } elseif ($user['role'] === 'pembina') {
            $b->where('k.pembina_id', (int) $user['id']);
        }

        return $b;
    }

    /** Ambil satu proposal jika user berhak mengaksesnya, selain itu null. */
    public function untukUser(int $id, array $user): ?array
    {
        return $this->scope($user)->where('p.id', $id)->get()->getRowArray();
    }

    public function daftar(array $user, ?string $status = null): array
    {
        $b = $this->scope($user)->orderBy('p.created_at', 'DESC');
        if ($status) {
            $b->where('p.status', $status);
        }
        return $b->get()->getResultArray();
    }

    /** Statistik jumlah proposal per status. */
    public function statistik(array $user): array
    {
        $baris = $this->scope($user, false)
            ->select('p.status, COUNT(*) AS jml', false)
            ->groupBy('p.status')
            ->get()->getResultArray();

        $hasil = [
            'diajukan' => 0, 'disetujui_pembina' => 0, 'ditolak_pembina' => 0,
            'divalidasi' => 0, 'ditolak_birmawa' => 0,
        ];
        foreach ($baris as $r) {
            $hasil[$r['status']] = (int) $r['jml'];
        }
        $hasil['total'] = array_sum($hasil);
        return $hasil;
    }

    /** Jumlah proposal masuk per bulan (6 bulan terakhir). */
    public function perBulan(array $user): array
    {
        $baris = $this->scope($user, false)
            ->select("DATE_FORMAT(p.created_at, '%Y-%m') AS bulan, COUNT(*) AS jml", false)
            ->where('p.created_at >=', date('Y-m-01', strtotime('-5 months')))
            ->groupBy('bulan')->orderBy('bulan', 'ASC')
            ->get()->getResultArray();

        $map = array_column($baris, 'jml', 'bulan');
        $out = [];
        for ($i = 5; $i >= 0; $i--) {
            $k = date('Y-m', strtotime("-$i months", strtotime(date('Y-m-01'))));
            $out[$k] = (int) ($map[$k] ?? 0);
        }
        return $out;
    }

    /** Daftar UKM yang pernah mengajukan proposal beserta rekap statusnya. */
    public function rekapPerUkm(array $user): array
    {
        return $this->scope($user, false)
            ->select("k.nama_ukm, COUNT(*) AS total,
                      SUM(p.status = 'divalidasi') AS disetujui,
                      SUM(p.status IN ('diajukan','disetujui_pembina')) AS diproses,
                      MAX(p.created_at) AS terakhir", false)
            ->groupBy('p.ukm_id, k.nama_ukm')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }
}
