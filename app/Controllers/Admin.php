<?php

namespace App\Controllers;

use App\Models\UkmModel;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

/** Data master (khusus Birmawa): UKM dan pengguna. */
class Admin extends AppController
{
    // ------------------------------------------------------------ UKM
    public function ukm()
    {
        $edit = (int) $this->request->getGet('edit');
        return view('admin/ukm', [
            'daftar'  => (new UkmModel())->daftarLengkap(),
            'pembina' => (new UserModel())->where('role', 'pembina')->orderBy('nama')->findAll(),
            'edit'    => $edit ? (new UkmModel())->find($edit) : null,
        ]);
    }

    public function ukmSimpan()
    {
        $rules = ['nama_ukm' => 'required|max_length[150]', 'pembina_id' => 'permit_empty|is_natural_no_zero'];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id   = (int) $this->request->getPost('id');
        $data = [
            'nama_ukm'   => trim((string) $this->request->getPost('nama_ukm')),
            'deskripsi'  => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'pembina_id' => $this->request->getPost('pembina_id') ?: null,
        ];

        $m = new UkmModel();
        $id ? $m->update($id, $data) : $m->insert($data);

        return redirect()->to('/admin/ukm')->with('success', 'Data UKM tersimpan.');
    }

    public function ukmHapus(int $id)
    {
        try {
            (new UkmModel())->delete($id);
        } catch (DatabaseException $e) {
            return redirect()->back()->with('error', 'UKM tidak dapat dihapus karena sudah memiliki proposal.');
        }
        return redirect()->to('/admin/ukm')->with('success', 'UKM dihapus.');
    }

    // ------------------------------------------------------------ PENGGUNA
    public function pengguna()
    {
        $edit = (int) $this->request->getGet('edit');
        return view('admin/pengguna', [
            'daftar' => (new UserModel())->daftarLengkap(),
            'ukm'    => (new UkmModel())->orderBy('nama_ukm')->findAll(),
            'edit'   => $edit ? (new UserModel())->find($edit) : null,
        ]);
    }

    public function penggunaSimpan()
    {
        $id = (int) $this->request->getPost('id');

        $rules = [
            'nama'  => 'required|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'  => 'required|in_list[ukm,pembina,birmawa]',
            'password' => $id ? 'permit_empty|min_length[8]' : 'required|min_length[8]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->request->getPost('role');
        $data = [
            'nama'   => trim((string) $this->request->getPost('nama')),
            'email'  => trim((string) $this->request->getPost('email')),
            'role'   => $role,
            'ukm_id' => $role === 'ukm' ? ($this->request->getPost('ukm_id') ?: null) : null,
        ];
        if ($role === 'ukm' && ! $data['ukm_id']) {
            return redirect()->back()->withInput()->with('error', 'Pengguna dengan role UKM harus dihubungkan ke sebuah UKM.');
        }
        if ($pw = (string) $this->request->getPost('password')) {
            $data['password'] = password_hash($pw, PASSWORD_DEFAULT);
        }

        $m = new UserModel();
        $id ? $m->update($id, $data) : $m->insert($data);

        return redirect()->to('/admin/pengguna')->with('success', 'Data pengguna tersimpan.');
    }

    public function penggunaHapus(int $id)
    {
        if ($id === $this->user()['id']) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        try {
            (new UserModel())->delete($id);
        } catch (DatabaseException $e) {
            return redirect()->back()->with('error', 'Pengguna tidak dapat dihapus.');
        }
        return redirect()->to('/admin/pengguna')->with('success', 'Pengguna dihapus.');
    }
}
