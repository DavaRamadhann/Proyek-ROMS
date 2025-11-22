<?php

namespace App\Domains\Reminder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = collect([]); 
        return view('pages.reminder.index', compact('reminders'));
    }

    public function create()
    {
        return view('pages.reminder.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('reminder.index')->with('success', 'Reminder berhasil ditambahkan');
    }

    public function edit($id)
    {
        $reminder = new \stdClass();
        $reminder->id = $id;
        $reminder->title = 'Dummy Reminder';
        return view('pages.reminder.edit', compact('reminder'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('reminder.index')->with('success', 'Reminder berhasil diupdate');
    }

    public function destroy($id)
    {
        return redirect()->route('reminder.index')->with('success', 'Reminder berhasil dihapus');
    }
}
