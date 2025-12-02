<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiIntegrationController extends Controller
{
    /**
     * Menampilkan halaman dokumentasi Integrasi API
     */
    public function index()
    {
        // Di sini nanti bisa ditambahkan logic untuk generate/revoke API Key
        // Untuk sekarang kita tampilkan dokumentasi statis dulu
        
        $apiUrl = url('/api/v1/orders');
        
        return view('pages.api.index', compact('apiUrl'));
    }
}
