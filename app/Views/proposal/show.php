<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<?php
use App\Models\DokumenModel;
$role = current_user()['role'];
?>
<a href="<?= site_url('proposal') ?>" class="text-decoration-none">&larr; Kembali ke daftar</a>
<div class="d-flex justify-content-between align-items-start my-3">
    <div>
        <h4 class="mb-1"><?= esc($p['judul']) ?></h4>
        <div class="text-muted"><?= esc($p['nama_ukm']) ?></div>
    </div>
    <div class="text-end">
        <?= status_badge($p['status']) ?>
        <?php if ($role === 'ukm' && in_array($p['status'], ['ditolak_pembina', 'ditolak_birmawa'], true)): ?>
            <div class="mt-2"><a href="<?= site_url('proposal/' . $p['id'] . '/ubah') ?>" class="btn btn-warning btn-sm">Revisi &amp; Ajukan Kembali</a></div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body">
            <h6>Informasi Kegiatan</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3">Pelaksanaan</dt>
                <dd class="col-sm-9"><?= tgl($p['tanggal_mulai']) ?> s/d <?= tgl($p['tanggal_selesai']) ?></dd>
                <dt class="col-sm-3">Lokasi</dt><dd class="col-sm-9"><?= esc($p['lokasi']) ?></dd>
                <dt class="col-sm-3">Anggaran</dt><dd class="col-sm-9"><?= rupiah($p['anggaran']) ?></dd>
                <dt class="col-sm-3">Sasaran</dt><dd class="col-sm-9"><?= esc($p['sasaran']) ?></dd>
                <dt class="col-sm-3">Latar Belakang</dt><dd class="col-sm-9"><?= nl2br(esc($p['latar_belakang'])) ?></dd>
                <dt class="col-sm-3">Tujuan</dt><dd class="col-sm-9"><?= nl2br(esc($p['tujuan'])) ?></dd>
            </dl>
        </div></div>

        <div class="card mb-3"><div class="card-body">
            <h6>Kelengkapan Dokumen
                <?= $lengkap ? '<span class="badge bg-success">Lengkap</span>' : '<span class="badge bg-danger">Belum lengkap</span>' ?>
            </h6>
            <ul class="list-group list-group-flush">
            <?php foreach (DokumenModel::JENIS as $jenis => $label):
                $d = null;
                foreach ($dokumen as $x) { if ($x['jenis'] === $jenis) { $d = $x; } } ?>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span><?= $d ? '✅' : (in_array($jenis, DokumenModel::WAJIB, true) ? '❌' : '➖') ?> <?= esc($label) ?></span>
                    <?php if ($d): ?>
                        <a href="<?= site_url('dokumen/' . $d['id']) ?>" class="btn btn-sm btn-outline-secondary">
                            Unduh &middot; <?= esc($d['nama_asli']) ?> (<?= number_format($d['ukuran'] / 1024, 0) ?> KB)
                        </a>
                    <?php else: ?><span class="text-muted small">belum diunggah</span><?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div></div>

        <?php /* ---------- Panel aksi PEMBINA ---------- */ ?>
        <?php if ($role === 'pembina' && $p['status'] === 'diajukan'): ?>
        <div class="card border-warning mb-3"><div class="card-body">
            <h6>Verifikasi Pembina</h6>
            <form method="post" action="<?= site_url('proposal/' . $p['id'] . '/review') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Catatan (wajib diisi jika menolak)</label>
                    <textarea name="catatan" rows="3" class="form-control"><?= esc(old('catatan')) ?></textarea>
                </div>
                <button name="aksi" value="setujui" class="btn btn-success">Setujui</button>
                <button name="aksi" value="tolak" class="btn btn-danger">Tolak</button>
            </form>
        </div></div>
        <?php endif; ?>

        <?php /* ---------- Panel aksi BIRMAWA ---------- */ ?>
        <?php if ($role === 'birmawa' && $p['status'] === 'disetujui_pembina'): ?>
        <div class="card border-info mb-3"><div class="card-body">
            <h6>Validasi Birmawa</h6>
            <?php if (! $lengkap): ?>
                <div class="alert alert-warning py-2">Dokumen wajib belum lengkap. Proposal hanya dapat dikembalikan untuk revisi.</div>
            <?php endif; ?>
            <form method="post" action="<?= site_url('proposal/' . $p['id'] . '/validasi') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Catatan (wajib diisi jika mengembalikan)</label>
                    <textarea name="catatan" rows="3" class="form-control"><?= esc(old('catatan')) ?></textarea>
                </div>
                <?php if ($lengkap): ?>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="sudah_diperiksa" value="1" id="chk">
                    <label class="form-check-label" for="chk">Saya telah memeriksa kelengkapan data dan dokumen proposal ini.</label>
                </div>
                <button name="aksi" value="validasi" class="btn btn-success">Validasi</button>
                <?php endif; ?>
                <button name="aksi" value="tolak" class="btn btn-danger">Kembalikan / Tolak</button>
            </form>
        </div></div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <?php if ($p['catatan_pembina'] || $p['catatan_birmawa']): ?>
        <div class="card mb-3"><div class="card-body">
            <h6>Catatan Terakhir</h6>
            <?php if ($p['catatan_pembina']): ?><p class="mb-2"><strong>Pembina:</strong><br><?= nl2br(esc($p['catatan_pembina'])) ?></p><?php endif; ?>
            <?php if ($p['catatan_birmawa']): ?><p class="mb-0"><strong>Birmawa:</strong><br><?= nl2br(esc($p['catatan_birmawa'])) ?></p><?php endif; ?>
        </div></div>
        <?php endif; ?>

        <div class="card"><div class="card-body">
            <h6>Riwayat Proses</h6>
            <div class="timeline mt-3">
            <?php foreach ($riwayat as $r): ?>
                <div>
                    <?= status_badge($r['status']) ?>
                    <div class="small text-muted"><?= date('d-m-Y H:i', strtotime($r['created_at'])) ?> &middot; <?= esc($r['nama'] ?? 'Pengguna dihapus') ?></div>
                    <?php if ($r['catatan']): ?><div class="small"><?= esc($r['catatan']) ?></div><?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>
