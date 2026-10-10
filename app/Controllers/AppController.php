<?php

namespace App\Controllers;

/**
 * Base controller aplikasi: memuat helper dan menyediakan data user login.
 */
abstract class AppController extends BaseController
{
    protected $helpers = ['url', 'form', 'ukm'];

    protected function user(): array
    {
        return session()->get('user');
    }
}
