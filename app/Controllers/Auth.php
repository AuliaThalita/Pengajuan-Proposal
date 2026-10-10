<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends AppController
{
    public function index()
    {
        return redirect()->to(session()->get('user') ? '/dashboard' : '/login');
    }

    public function login()
    {
        if (session()->get('user')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function attempt()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Email dan password wajib diisi dengan benar.');
        }

        $user = (new UserModel())->where('email', $this->request->getPost('email'))->first();

        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        session()->regenerate();
        session()->set('user', [
            'id'     => (int) $user['id'],
            'nama'   => $user['nama'],
            'email'  => $user['email'],
            'role'   => $user['role'],
            'ukm_id' => $user['ukm_id'] ? (int) $user['ukm_id'] : null,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah keluar.');
    }
}
