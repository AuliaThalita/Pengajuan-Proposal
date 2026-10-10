<?php

if (! function_exists('status_label')) {
    function status_label(string $status): string
    {
        return [
            'diajukan'          => 'Menunggu Verifikasi Pembina',
            'disetujui_pembina' => 'Disetujui Pembina (Menunggu Validasi Birmawa)',
            'ditolak_pembina'   => 'Ditolak Pembina',
            'divalidasi'        => 'Divalidasi Birmawa',
            'ditolak_birmawa'   => 'Ditolak / Perlu Revisi (Birmawa)',
        ][$status] ?? $status;
    }
}

if (! function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $warna = [
            'diajukan'          => 'warning text-dark',
            'disetujui_pembina' => 'info text-dark',
            'ditolak_pembina'   => 'danger',
            'divalidasi'        => 'success',
            'ditolak_birmawa'   => 'danger',
        ][$status] ?? 'secondary';

        return '<span class="badge bg-' . $warna . '">' . esc(status_label($status)) . '</span>';
    }
}

if (! function_exists('rupiah')) {
    function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('tgl')) {
    function tgl(?string $tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }
        return date('d-m-Y', strtotime($tanggal));
    }
}

if (! function_exists('current_user')) {
    function current_user(): ?array
    {
        return session()->get('user');
    }
}
