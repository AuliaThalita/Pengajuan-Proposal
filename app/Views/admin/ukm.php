<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Data UKM</h4>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h6><?= $edit ? 'Ubah UKM' : 'Tambah UKM' ?></h6>
            <form method="post" action="<?= site_url('admin/ukm') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                <div class="mb-3">
                    <label class="form-label">Nama UKM *</label>
                    <input name="nama_ukm" class="form-control" value="<?= esc(old('nama_ukm', $edit['nama_ukm'] ?? '')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control"><?= esc(old('deskripsi', $edit['deskripsi'] ?? '')) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pembina</label>
                    <select name="pembina_id" class="form-select">
                        <option value="">- belum ada -</option>
                        <?php foreach ($pembina as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= (string) old('pembina_id', $edit['pembina_id'] ?? '') === (string) $b['id'] ? 'selected' : '' ?>><?= esc($b['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary">Simpan</button>
                <?php if ($edit): ?><a href="<?= site_url('admin/ukm') ?>" class="btn btn-light">Batal</a><?php endif; ?>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>UKM</th><th>Pembina</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($daftar as $k): ?>
                <tr>
                    <td><strong><?= esc($k['nama_ukm']) ?></strong><div class="small text-muted"><?= esc($k['deskripsi']) ?></div></td>
                    <td><?= esc($k['nama_pembina'] ?? '-') ?></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= site_url('admin/ukm?edit=' . $k['id']) ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form method="post" action="<?= site_url('admin/ukm/' . $k['id'] . '/hapus') ?>" class="d-inline" onsubmit="return confirm('Hapus UKM ini?')">
                            <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>
