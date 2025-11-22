<?php

namespace App\Domains\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Placeholder
        $products = collect([]); 
        return view('pages.product.index', compact('products'));
    }

    public function create()
    {
        return view('pages.product.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('product.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $product = new \stdClass();
        $product->id = $id;
        $product->name = 'Dummy Product';
        return view('pages.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('product.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($id)
    {
        return redirect()->route('product.index')->with('success', 'Produk berhasil dihapus');
    }
}
