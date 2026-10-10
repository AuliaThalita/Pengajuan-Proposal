<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SI Pengajuan UKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:linear-gradient(135deg,#212529,#0d6efd);min-height:100vh}</style>
</head>
<body class="d-flex align-items-center">
<div class="container" style="max-width:420px">
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <h4 class="mb-0">SI Pengajuan UKM</h4>
            <p class="text-muted">Biro Kemahasiswaan &mdash; Universitas Mercu Buana</p>

            <?php if ($m = session()->getFlashdata('error')): ?><div class="alert alert-danger py-2"><?= esc($m) ?></div><?php endif; ?>
            <?php if ($m = session()->getFlashdata('success')): ?><div class="alert alert-success py-2"><?= esc($m) ?></div><?php endif; ?>

            <form method="post" action="<?= site_url('login') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100">Masuk</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
