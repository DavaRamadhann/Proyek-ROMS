<?php

namespace App\Domains\Reminder\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Reminder\Models\ReminderLog;
use Illuminate\Http\Request;

use App\Domains\Reminder\Models\Reminder;
use App\Domains\Product\Models\Product;

class ReminderController extends Controller
{
    public function index()
    {
        // Menampilkan Rules (Aturan)
        $rules = Reminder::with('product')->get();

        // Menampilkan reminder yang akan datang (pending) dan history (sent/failed)
        $reminders = ReminderLog::with(['customer', 'order', 'reminder'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);
            
        return view('pages.reminder.index', compact('reminders', 'rules'));
    }

    public function create()
    {
        $products = Product::all();
        $templates = \App\Domains\Message\Models\MessageTemplate::whereIn('type', ['general', 'reminder'])->get();
        return view('pages.reminder.create', compact('products', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id', // Produk pemicu
            'cross_sell_product_id' => 'nullable|exists:products,id', // Produk yang ditawarkan (NEW)
            'days_after_delivery' => 'required|integer|min:1|max:365',
            'message_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Reminder::create($validated);

        return redirect()->route('reminders.index')->with('success', 'Reminder rule berhasil dibuat.');
    }

    public function edit(Reminder $reminder)
    {
        $products = Product::all();
        $templates = \App\Domains\Message\Models\MessageTemplate::whereIn('type', ['general', 'reminder'])->get();
        return view('pages.reminder.edit', compact('reminder', 'products', 'templates'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'cross_sell_product_id' => 'nullable|exists:products,id', // Produk yang ditawarkan (NEW)
            'days_after_delivery' => 'required|integer|min:1|max:365',
            'message_template' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $reminder->update($validated);

        return redirect()->route('reminders.index')->with('success', 'Reminder rule berhasil diperbarui.');
    }

    public function destroy(ReminderLog $reminder)
    {
        if ($reminder->status != 'pending') {
            return back()->with('error', 'Hanya reminder yang masih pending yang dapat dihapus.');
        }

        $reminder->delete();

        return back()->with('success', 'Reminder berhasil dibatalkan.');
    }
}
