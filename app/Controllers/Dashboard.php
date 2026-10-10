<?php

namespace App\Controllers;

use App\Models\ProposalModel;

class Dashboard extends AppController
{
    public function index()
    {
        $user  = $this->user();
        $model = new ProposalModel();

        $stat = $model->statistik($user);

        // Proposal yang masih dalam proses (verifikasi pembina / validasi Birmawa)
        $diproses = array_filter(
            $model->daftar($user),
            static fn ($p) => in_array($p['status'], ['diajukan', 'disetujui_pembina'], true)
        );

        return view('dashboard/index', [
            'stat'     => $stat,
            'bulan'    => $model->perBulan($user),
            'rekap'    => $model->rekapPerUkm($user),
            'diproses' => array_slice($diproses, 0, 8),
        ]);
    }
}
