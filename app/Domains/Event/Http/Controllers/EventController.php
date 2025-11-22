<?php

namespace App\Domains\Event\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('pages.event.index');
    }

    public function create()
    {
        return view('pages.event.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.daftar-acara')->with('success', 'Acara berhasil ditambahkan');
    }
}
