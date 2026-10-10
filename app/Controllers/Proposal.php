<?php

namespace App\Controllers;

use App\Models\DokumenModel;
use App\Models\LogModel;
use App\Models\ProposalModel;

class Proposal extends AppController
{
    private const EXT_BOLEH = ['pdf', 'doc', 'docx'];
    private const MAKS_KB   = 5120; // 5 MB

    private ProposalModel $proposals;
    private DokumenModel $dokumen;
    private LogModel $log;

    public function __construct()
    {
        $this->proposals = new ProposalModel();
        $this->dokumen   = new DokumenModel();
        $this->log       = new LogModel();
    }

    // ------------------------------------------------------------ DAFTAR & DETAIL
    public function index()
    {
        $status = $this->request->getGet('status');
        $valid  = ['diajukan', 'disetujui_pembina', 'ditolak_pembina', 'divalidasi', 'ditolak_birmawa'];
        if (! in_array($status, $valid, true)) {
            $status = null;
        }

        return view('proposal/index', [
            'daftar' => $this->proposals->daftar($this->user(), $status),
            'status' => $status,
        ]);
    }

    public function show(int $id)
    {
        $p = $this->proposals->untukUser($id, $this->user());
        if (! $p) {
            return redirect()->to('/proposal')->with('error', 'Proposal tidak ditemukan.');
        }

        $dok    = $this->dokumen->where('proposal_id', $id)->findAll();
        $jenisAda = array_column($dok, 'jenis');

        return view('proposal/show', [
            'p'        => $p,
            'dokumen'  => $dok,
            'lengkap'  => ! array_diff(DokumenModel::WAJIB, $jenisAda),
            'jenisAda' => $jenisAda,
            'riwayat'  => $this->log->riwayat($id),
        ]);
    }

    // ------------------------------------------------------------ UKM: AJUKAN
    public function create()
    {
        return view('proposal/form', ['p' => null, 'jenisAda' => []]);
    }

    public function store()
    {
        $user = $this->user();
        if (! $user['ukm_id']) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung ke UKM. Hubungi Birmawa.');
        }
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $errFile = $this->cekFile([]);
        if ($errFile) {
            return redirect()->back()->withInput()->with('errors', $errFile);
        }

        $db = db_connect();
        $db->transStart();

        $id = $this->proposals->insert($this->dataForm() + [
            'ukm_id'        => $user['ukm_id'],
            'diajukan_oleh' => $user['id'],
            'status'        => 'diajukan',
        ]);
        $this->simpanFile((int) $id);
        $this->log->catat((int) $id, $user['id'], 'diajukan', 'Proposal diajukan.');

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan proposal.');
        }

        return redirect()->to('/proposal/' . $id)->with('success', 'Proposal berhasil diajukan dan menunggu verifikasi pembina.');
    }

    // ------------------------------------------------------------ UKM: REVISI
    public function edit(int $id)
    {
        $p = $this->proposalMilikUkm($id);
        if (! $p) {
            return redirect()->to('/proposal')->with('error', 'Proposal ini tidak dapat diubah.');
        }
        return view('proposal/form', [
            'p'        => $p,
            'jenisAda' => array_column($this->dokumen->where('proposal_id', $id)->findAll(), 'jenis'),
        ]);
    }

    public function update(int $id)
    {
        $user = $this->user();
        $p    = $this->proposalMilikUkm($id);
        if (! $p) {
            return redirect()->to('/proposal')->with('error', 'Proposal ini tidak dapat diubah.');
        }
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jenisAda = array_column($this->dokumen->where('proposal_id', $id)->findAll(), 'jenis');
        $errFile  = $this->cekFile($jenisAda);
        if ($errFile) {
            return redirect()->back()->withInput()->with('errors', $errFile);
        }

        $db = db_connect();
        $db->transStart();

        $this->proposals->update($id, $this->dataForm() + [
            'status'          => 'diajukan',
            'catatan_pembina' => null,
            'catatan_birmawa' => null,
        ]);
        $this->simpanFile($id);
        $this->log->catat($id, $user['id'], 'diajukan', 'Proposal direvisi dan diajukan kembali.');

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan revisi.');
        }

        return redirect()->to('/proposal/' . $id)->with('success', 'Revisi berhasil diajukan kembali.');
    }

    // ------------------------------------------------------------ PEMBINA: VERIFIKASI
    public function review(int $id)
    {
        $user = $this->user();
        $p    = $this->proposals->untukUser($id, $user);
        if (! $p || $p['status'] !== 'diajukan') {
            return redirect()->to('/proposal')->with('error', 'Proposal tidak dapat diverifikasi.');
        }

        $aksi    = $this->request->getPost('aksi');
        $catatan = trim((string) $this->request->getPost('catatan'));

        if (! in_array($aksi, ['setujui', 'tolak'], true)) {
            return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
        if ($aksi === 'tolak' && $catatan === '') {
            return redirect()->back()->withInput()->with('error', 'Catatan wajib diisi saat menolak proposal.');
        }

        $status = $aksi === 'setujui' ? 'disetujui_pembina' : 'ditolak_pembina';
        $this->proposals->update($id, ['status' => $status, 'catatan_pembina' => $catatan ?: null]);
        $this->log->catat($id, $user['id'], $status, $catatan ?: null);

        return redirect()->to('/proposal/' . $id)->with('success', 'Hasil verifikasi tersimpan.');
    }

    // ------------------------------------------------------------ BIRMAWA: VALIDASI
    public function validasi(int $id)
    {
        $user = $this->user();
        $p    = $this->proposals->untukUser($id, $user);
        if (! $p || $p['status'] !== 'disetujui_pembina') {
            return redirect()->to('/proposal')->with('error', 'Proposal belum dapat divalidasi (belum disetujui pembina).');
        }

        $aksi    = $this->request->getPost('aksi');
        $catatan = trim((string) $this->request->getPost('catatan'));

        if (! in_array($aksi, ['validasi', 'tolak'], true)) {
            return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
        if ($aksi === 'tolak' && $catatan === '') {
            return redirect()->back()->withInput()->with('error', 'Catatan wajib diisi saat mengembalikan proposal.');
        }
        if ($aksi === 'validasi') {
            $jenisAda = array_column($this->dokumen->where('proposal_id', $id)->findAll(), 'jenis');
            if (array_diff(DokumenModel::WAJIB, $jenisAda)) {
                return redirect()->back()->with('error', 'Dokumen wajib belum lengkap, proposal tidak dapat divalidasi.');
            }
            if (! $this->request->getPost('sudah_diperiksa')) {
                return redirect()->back()->with('error', 'Centang pernyataan bahwa kelengkapan dokumen sudah diperiksa.');
            }
        }

        $status = $aksi === 'validasi' ? 'divalidasi' : 'ditolak_birmawa';
        $this->proposals->update($id, ['status' => $status, 'catatan_birmawa' => $catatan ?: null]);
        $this->log->catat($id, $user['id'], $status, $catatan ?: null);

        return redirect()->to('/proposal/' . $id)->with('success', 'Hasil validasi tersimpan.');
    }

    // ------------------------------------------------------------ HELPER
    /** Proposal milik UKM user yang boleh direvisi (status ditolak). */
    private function proposalMilikUkm(int $id): ?array
    {
        $p = $this->proposals->untukUser($id, $this->user());
        return ($p && in_array($p['status'], ['ditolak_pembina', 'ditolak_birmawa'], true)) ? $p : null;
    }

    private function aturan(): array
    {
        return [
            'judul'           => 'required|max_length[200]',
            'latar_belakang'  => 'required',
            'tujuan'          => 'required',
            'sasaran'         => 'required|max_length[255]',
            'tanggal_mulai'   => 'required|valid_date[Y-m-d]',
            'tanggal_selesai' => 'required|valid_date[Y-m-d]',
            'lokasi'          => 'required|max_length[200]',
            'anggaran'        => 'required|numeric|greater_than_equal_to[0]',
        ];
    }

    private function dataForm(): array
    {
        $g = fn ($k) => trim((string) $this->request->getPost($k));
        return [
            'judul'           => $g('judul'),
            'latar_belakang'  => $g('latar_belakang'),
            'tujuan'          => $g('tujuan'),
            'sasaran'         => $g('sasaran'),
            'tanggal_mulai'   => $g('tanggal_mulai'),
            'tanggal_selesai' => $g('tanggal_selesai'),
            'lokasi'          => $g('lokasi'),
            'anggaran'        => $g('anggaran'),
        ];
    }

    /** Validasi file upload: ekstensi, ukuran, dan kewajiban dokumen. */
    private function cekFile(array $jenisSudahAda): array
    {
        $err = [];
        foreach (DokumenModel::JENIS as $jenis => $label) {
            $f = $this->request->getFile('dok_' . $jenis);
            $adaFile = $f && $f->isValid() && ! $f->hasMoved() && $f->getSize() > 0;

            if (! $adaFile) {
                if (in_array($jenis, DokumenModel::WAJIB, true) && ! in_array($jenis, $jenisSudahAda, true)) {
                    $err['dok_' . $jenis] = "Dokumen {$label} wajib diunggah.";
                }
                continue;
            }
            if (! in_array(strtolower($f->getClientExtension()), self::EXT_BOLEH, true)) {
                $err['dok_' . $jenis] = "{$label}: hanya file PDF/DOC/DOCX yang diperbolehkan.";
            } elseif ($f->getSize() > self::MAKS_KB * 1024) {
                $err['dok_' . $jenis] = "{$label}: ukuran maksimal 5 MB.";
            }
        }
        return $err;
    }

    /** Simpan file yang diunggah; dokumen lama dengan jenis sama digantikan. */
    private function simpanFile(int $proposalId): void
    {
        $dir = WRITEPATH . 'uploads/proposal/' . $proposalId;
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        foreach (array_keys(DokumenModel::JENIS) as $jenis) {
            $f = $this->request->getFile('dok_' . $jenis);
            if (! $f || ! $f->isValid() || $f->hasMoved() || $f->getSize() === 0) {
                continue;
            }

            foreach ($this->dokumen->where(['proposal_id' => $proposalId, 'jenis' => $jenis])->findAll() as $lama) {
                @unlink($dir . '/' . $lama['nama_file']);
                $this->dokumen->delete($lama['id']);
            }

            $nama = $f->getRandomName();
            $asli = $f->getClientName();
            $ukuran = $f->getSize();
            $f->move($dir, $nama);

            $this->dokumen->insert([
                'proposal_id' => $proposalId,
                'jenis'       => $jenis,
                'nama_asli'   => $asli,
                'nama_file'   => $nama,
                'ukuran'      => $ukuran,
            ]);
        }
    }
}
