<?php

namespace App\Domains\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Product\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        // Gunakan orderBy id desc karena tidak ada created_at
        $products = Product::orderBy('id', 'desc')->paginate(10);
        
        // Ambil 5 produk dengan stok paling sedikit
        $lowStockProducts = Product::orderBy('stock', 'asc')->take(5)->get();

        return view('pages.product.index', compact('products', 'lowStockProducts'));
    }

    public function create()
    {
        return view('pages.product.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'recommendation_text' => 'nullable|string',
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
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'recommendation_text' => 'nullable|string',
        ]);

        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            
            return redirect()->route('product.index')->with('success', 'Produk berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Kode error 23503 adalah foreign key violation di PostgreSQL
            if ($e->getCode() == '23503') {
                return back()->with('error', 'Produk tidak dapat dihapus karena sedang digunakan dalam pesanan.');
            }
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
