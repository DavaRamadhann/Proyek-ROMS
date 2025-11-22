<?php

namespace App\Domains\Automation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index()
    {
        return view('pages.automation.index');
    }

    public function create()
    {
        return view('pages.automation.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.otomasi-pesan')->with('success', 'Otomasi berhasil ditambahkan');
    }
}
