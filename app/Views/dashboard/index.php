<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<?php $role = current_user()['role']; ?>
<h4 class="mb-3">Dashboard Monitoring
    <small class="text-muted fs-6">
        <?= $role === 'birmawa' ? '(seluruh UKM)' : ($role === 'pembina' ? '(UKM binaan Anda)' : '(UKM Anda)') ?>
    </small>
</h4>

<div class="row g-3 mb-4">
    <?php
    $kartu = [
        ['Proposal Masuk',        $stat['total'],             'primary',   ''],
        ['Disetujui (Divalidasi)', $stat['divalidasi'],       'success',   'divalidasi'],
        ['Menunggu Pembina',      $stat['diajukan'],          'warning',   'diajukan'],
        ['Menunggu Birmawa',      $stat['disetujui_pembina'], 'info',      'disetujui_pembina'],
        ['Ditolak / Revisi',      $stat['ditolak_pembina'] + $stat['ditolak_birmawa'], 'danger', ''],
    ];
    foreach ($kartu as [$label, $angka, $warna, $filter]): ?>
        <div class="col-6 col-md">
            <a class="text-decoration-none" href="<?= site_url('proposal') . ($filter ? '?status=' . $filter : '') ?>">
                <div class="card h-100 border-start border-4 border-<?= $warna ?>">
                    <div class="card-body">
                        <div class="text-muted small"><?= esc($label) ?></div>
                        <div class="stat-num text-dark"><?= (int) $angka ?></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="card h-100"><div class="card-body">
            <h6>Komposisi Status Proposal</h6>
            <?php if ($stat['total'] > 0): ?>
                <canvas id="chartStatus" height="220"></canvas>
            <?php else: ?><p class="text-muted mb-0">Belum ada data.</p><?php endif; ?>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card h-100"><div class="card-body">
            <h6>Proposal Masuk per Bulan (6 bulan terakhir)</h6>
            <canvas id="chartBulan" height="120"></canvas>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card"><div class="card-body">
            <h6>Proposal Dalam Proses Verifikasi / Persetujuan</h6>
            <?php if (! $diproses): ?><p class="text-muted mb-0">Tidak ada proposal yang sedang diproses.</p>
            <?php else: ?>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0">
                <thead><tr><th>Judul</th><th>UKM</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($diproses as $p): ?>
                    <tr>
                        <td><a href="<?= site_url('proposal/' . $p['id']) ?>"><?= esc($p['judul']) ?></a></td>
                        <td><?= esc($p['nama_ukm']) ?></td>
                        <td><?= status_badge($p['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
            <?php endif; ?>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-body">
            <h6>Daftar UKM yang Telah Mengajukan Proposal</h6>
            <?php if (! $rekap): ?><p class="text-muted mb-0">Belum ada UKM yang mengajukan proposal.</p>
            <?php else: ?>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0">
                <thead><tr><th>UKM</th><th class="text-center">Total</th><th class="text-center">Disetujui</th><th class="text-center">Diproses</th><th>Terakhir</th></tr></thead>
                <tbody>
                <?php foreach ($rekap as $r): ?>
                    <tr>
                        <td><?= esc($r['nama_ukm']) ?></td>
                        <td class="text-center"><?= (int) $r['total'] ?></td>
                        <td class="text-center"><?= (int) $r['disetujui'] ?></td>
                        <td class="text-center"><?= (int) $r['diproses'] ?></td>
                        <td><?= tgl($r['terakhir']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
            <?php endif; ?>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const statusEl = document.getElementById('chartStatus');
if (statusEl) {
    new Chart(statusEl, {
        type: 'doughnut',
        data: {
            labels: ['Menunggu Pembina', 'Menunggu Birmawa', 'Divalidasi', 'Ditolak Pembina', 'Ditolak/Revisi Birmawa'],
            datasets: [{ data: <?= json_encode([
                $stat['diajukan'], $stat['disetujui_pembina'], $stat['divalidasi'],
                $stat['ditolak_pembina'], $stat['ditolak_birmawa'],
            ]) ?>, backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545', '#fd7e14'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}
new Chart(document.getElementById('chartBulan'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($bulan)) ?>,
        datasets: [{ label: 'Jumlah proposal', data: <?= json_encode(array_values($bulan)) ?>, backgroundColor: '#0d6efd' }]
    },
    options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
});
</script>
<?= $this->endSection() ?>
