<?php $u = current_user(); $uri = service('uri')->getPath(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SI Pengajuan UKM') ?> | Birmawa UMB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f6f9; }
        .navbar-brand small { font-weight:400; opacity:.8; }
        .card { border:0; box-shadow:0 1px 4px rgba(0,0,0,.08); }
        .stat-num { font-size:2rem; font-weight:700; line-height:1; }
        .timeline { border-left:3px solid #dee2e6; padding-left:1rem; }
        .timeline > div { position:relative; margin-bottom:1rem; }
        .timeline > div::before { content:''; position:absolute; left:-1.4rem; top:.35rem; width:.8rem; height:.8rem; border-radius:50%; background:#0d6efd; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('dashboard') ?>">SI Pengajuan UKM <small>Birmawa UMB</small></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                <?php if ($u['role'] === 'ukm'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('proposal') ?>">Proposal Saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('proposal/baru') ?>">Ajukan Proposal</a></li>
                <?php elseif ($u['role'] === 'pembina'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('proposal') ?>">Verifikasi Proposal</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('proposal') ?>">Validasi Proposal</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/ukm') ?>">Data UKM</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/pengguna') ?>">Pengguna</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text me-3 text-white-50">
                <?= esc($u['nama']) ?> <span class="badge bg-secondary"><?= esc(strtoupper($u['role'])) ?></span>
            </span>
            <a class="btn btn-outline-light btn-sm" href="<?= site_url('logout') ?>">Keluar</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if ($m = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= esc($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($m = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= esc($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($errs = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger"><ul class="mb-0">
            <?php foreach ((array) $errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<footer class="text-center text-muted small pb-4">Sistem Informasi Pengajuan &amp; Monitoring Kegiatan UKM &mdash; Biro Kemahasiswaan Universitas Mercu Buana</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
