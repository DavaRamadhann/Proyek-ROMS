<?php

namespace App\Domains\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Customer\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all(); 
        return view('pages.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('pages.customer.create');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan
        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan');
    }

    public function edit($id)
    {
        // Placeholder
        $customer = new \stdClass();
        $customer->id = $id;
        $customer->name = 'Dummy Customer';
        return view('pages.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        // Update logic
        return redirect()->route('customer.index')->with('success', 'Customer berhasil diupdate');
    }

    public function destroy($id)
    {
        // Delete logic
        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus');
    }
}
