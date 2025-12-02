<?php

namespace App\Domains\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Customer\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name', 'asc')->paginate(10);

        return view('pages.customer.index', compact('customers'));
    }

    public function edit(Customer $customer)
    {
        return view('pages.customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // Jika nama berubah, set is_manual_name = true
        if ($customer->name !== $validated['name']) {
            $validated['is_manual_name'] = true;
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        // Cek apakah ada order atau chat room terkait
        if ($customer->orders()->exists() || $customer->chatRooms()->exists()) {
            return back()->with('error', 'Pelanggan tidak dapat dihapus karena memiliki riwayat pesanan atau chat.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
