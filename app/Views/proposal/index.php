<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<?php $role = current_user()['role']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        <?= $role === 'ukm' ? 'Riwayat Pengajuan Proposal' : ($role === 'pembina' ? 'Verifikasi Proposal' : 'Validasi Proposal') ?>
    </h4>
    <?php if ($role === 'ukm'): ?>
        <a href="<?= site_url('proposal/baru') ?>" class="btn btn-primary">+ Ajukan Proposal</a>
    <?php endif; ?>
</div>

<div class="card mb-3"><div class="card-body py-2">
    <form method="get" class="row g-2 align-items-center">
        <div class="col-auto"><label class="col-form-label">Filter status:</label></div>
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua</option>
                <?php foreach (['diajukan', 'disetujui_pembina', 'ditolak_pembina', 'divalidasi', 'ditolak_birmawa'] as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= esc(status_label($s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div></div>

<div class="card"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr>
        <th>#</th><th>Judul Kegiatan</th><th>UKM</th><th>Pelaksanaan</th><th class="text-end">Anggaran</th><th>Status</th><th></th>
    </tr></thead>
    <tbody>
    <?php if (! $daftar): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada proposal.</td></tr>
    <?php endif; ?>
    <?php foreach ($daftar as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><a href="<?= site_url('proposal/' . $p['id']) ?>"><?= esc($p['judul']) ?></a></td>
            <td><?= esc($p['nama_ukm']) ?></td>
            <td><?= tgl($p['tanggal_mulai']) ?><?= $p['tanggal_selesai'] !== $p['tanggal_mulai'] ? ' s/d ' . tgl($p['tanggal_selesai']) : '' ?></td>
            <td class="text-end"><?= rupiah($p['anggaran']) ?></td>
            <td><?= status_badge($p['status']) ?></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('proposal/' . $p['id']) ?>">Detail</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div></div>
<?= $this->endSection() ?>
