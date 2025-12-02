<?php

namespace App\Domains\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Product\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('pages.product.index', compact('products'));
    }

    public function create()
    {
        return view('pages.product.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
        ]);

        Product::create($validated);

        return redirect()->route('product.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
        ]);

        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return redirect()->route('product.index')->with('success', 'Produk berhasil dihapus');
    }
}
