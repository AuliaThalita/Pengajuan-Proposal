<?php

namespace App\Controllers;

use App\Models\DokumenModel;
use App\Models\ProposalModel;

class Dokumen extends AppController
{
    public function unduh(int $id)
    {
        $dok = (new DokumenModel())->find($id);
        if (! $dok || ! (new ProposalModel())->untukUser((int) $dok['proposal_id'], $this->user())) {
            return redirect()->to('/dashboard')->with('error', 'Dokumen tidak ditemukan atau Anda tidak berhak mengaksesnya.');
        }

        $path = WRITEPATH . 'uploads/proposal/' . $dok['proposal_id'] . '/' . $dok['nama_file'];
        if (! is_file($path)) {
            return redirect()->back()->with('error', 'File fisik dokumen tidak ditemukan di server.');
        }

        return $this->response->download($path, null)->setFileName($dok['nama_asli']);
    }
}
