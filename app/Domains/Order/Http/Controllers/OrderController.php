<?php

namespace App\Domains\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = collect([]); 
        return view('pages.order.index', compact('orders'));
    }

    public function create()
    {
        return view('pages.order.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('order.index')->with('success', 'Order berhasil ditambahkan');
    }

    public function edit($id)
    {
        $order = new \stdClass();
        $order->id = $id;
        $order->code = 'ORD-001';
        return view('pages.order.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('order.index')->with('success', 'Order berhasil diupdate');
    }

    public function destroy($id)
    {
        return redirect()->route('order.index')->with('success', 'Order berhasil dihapus');
    }
}
