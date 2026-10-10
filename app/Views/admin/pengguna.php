<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Pengguna</h4>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h6><?= $edit ? 'Ubah Pengguna' : 'Tambah Pengguna' ?></h6>
            <form method="post" action="<?= site_url('admin/pengguna') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                <div class="mb-3"><label class="form-label">Nama *</label>
                    <input name="nama" class="form-control" value="<?= esc(old('nama', $edit['nama'] ?? '')) ?>" required></div>
                <div class="mb-3"><label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email', $edit['email'] ?? '')) ?>" required></div>
                <div class="mb-3"><label class="form-label">Password <?= $edit ? '(kosongkan jika tidak diubah)' : '*' ?></label>
                    <input type="password" name="password" class="form-control" minlength="8" <?= $edit ? '' : 'required' ?>></div>
                <?php $roleSel = old('role', $edit['role'] ?? 'ukm'); ?>
                <div class="mb-3"><label class="form-label">Role *</label>
                    <select name="role" class="form-select">
                        <?php foreach (['ukm' => 'UKM', 'pembina' => 'Pembina UKM', 'birmawa' => 'Birmawa'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $roleSel === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="mb-3"><label class="form-label">UKM (khusus role UKM)</label>
                    <select name="ukm_id" class="form-select">
                        <option value="">-</option>
                        <?php foreach ($ukm as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= (string) old('ukm_id', $edit['ukm_id'] ?? '') === (string) $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_ukm']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <button class="btn btn-primary">Simpan</button>
                <?php if ($edit): ?><a href="<?= site_url('admin/pengguna') ?>" class="btn btn-light">Batal</a><?php endif; ?>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>Role</th><th>UKM</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($daftar as $u): ?>
                <tr>
                    <td><?= esc($u['nama']) ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td><span class="badge bg-secondary"><?= esc($u['role']) ?></span></td>
                    <td><?= esc($u['nama_ukm'] ?? '-') ?></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= site_url('admin/pengguna?edit=' . $u['id']) ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form method="post" action="<?= site_url('admin/pengguna/' . $u['id'] . '/hapus') ?>" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
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
