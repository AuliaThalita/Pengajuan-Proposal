<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<?php
use App\Models\DokumenModel;
$edit = $p !== null;
$val  = fn ($k) => old($k, $p[$k] ?? '');
?>
<h4 class="mb-3"><?= $edit ? 'Revisi Proposal Kegiatan' : 'Ajukan Proposal Kegiatan' ?></h4>

<?php if ($edit): ?>
    <div class="alert alert-warning">
        <strong>Catatan penolakan:</strong>
        <?= esc($p['catatan_pembina'] ?: $p['catatan_birmawa'] ?: '-') ?>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      action="<?= $edit ? site_url('proposal/' . $p['id'] . '/ubah') : site_url('proposal') ?>">
    <?= csrf_field() ?>
    <div class="card mb-3"><div class="card-body">
        <h6 class="mb-3">Informasi Kegiatan</h6>
        <div class="mb-3">
            <label class="form-label">Judul Kegiatan *</label>
            <input type="text" name="judul" class="form-control" maxlength="200" value="<?= esc($val('judul')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Latar Belakang *</label>
            <textarea name="latar_belakang" rows="4" class="form-control" required><?= esc($val('latar_belakang')) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Tujuan *</label>
            <textarea name="tujuan" rows="3" class="form-control" required><?= esc($val('tujuan')) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Sasaran *</label>
            <input type="text" name="sasaran" class="form-control" maxlength="255" value="<?= esc($val('sasaran')) ?>" required>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Mulai *</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?= esc($val('tanggal_mulai')) ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Selesai *</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="<?= esc($val('tanggal_selesai')) ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Lokasi *</label>
                <input type="text" name="lokasi" class="form-control" maxlength="200" value="<?= esc($val('lokasi')) ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Rencana Anggaran (Rp) *</label>
                <input type="number" name="anggaran" min="0" step="1000" class="form-control" value="<?= esc($val('anggaran')) ?>" required>
            </div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h6 class="mb-1">Dokumen Pendukung</h6>
        <p class="text-muted small">Format PDF / DOC / DOCX, maksimal 5 MB per file.
            <?= $edit ? 'Kosongkan jika tidak ingin mengganti dokumen yang sudah ada.' : '' ?></p>
        <?php foreach (DokumenModel::JENIS as $jenis => $label):
            $wajib = in_array($jenis, DokumenModel::WAJIB, true);
            $ada   = in_array($jenis, $jenisAda, true); ?>
            <div class="mb-3">
                <label class="form-label"><?= esc($label) ?> <?= $wajib ? '*' : '' ?>
                    <?php if ($ada): ?><span class="badge bg-success">sudah ada</span><?php endif; ?>
                </label>
                <input type="file" name="dok_<?= $jenis ?>" class="form-control" accept=".pdf,.doc,.docx"
                       <?= $wajib && ! $ada ? 'required' : '' ?>>
            </div>
        <?php endforeach; ?>
    </div></div>

    <a href="<?= site_url('proposal') ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><?= $edit ? 'Ajukan Kembali' : 'Ajukan Proposal' ?></button>
</form>
<?= $this->endSection() ?>
