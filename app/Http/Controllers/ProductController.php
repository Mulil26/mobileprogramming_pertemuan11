<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('product.index', compact('products'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png',
            'descriptions' => 'required|string|max:1000',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        // Upload image
        if ($request->hasFile('image')) {
            $validate['image'] = $request->file('image')->store('products', 'public');
        }

        // Default stock
        $validate['stock'] = $request->stock ?? 0;

        Product::create($validate);

        return redirect()->route('product.index')
            ->with('success', 'Data Product berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validate = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png',
            'descriptions' => 'required|string|max:1000',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        // Update file
        if ($request->hasFile('image')) {

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $validate['image'] = $request->file('image')->store('products', 'public');
        } else {
            $validate['image'] = $product->image;
        }

        $product->update($validate);

        return redirect()->route('product.index')
            ->with('success', 'Data Product berhasil diperbarui!');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('product.show', compact('product'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('product.index')
            ->with('success', 'Data Product berhasil dihapus!');
    }

    public function cetakPdf()
    {

        ini_set('memory_limit', '512M');

        $products = Product::all();
        $pdf = Pdf::loadView('product.cetak', ['products' => $products])
            ->setPaper('a4', 'landscape');
        return $pdf->download('data-product.pdf');
    }
}
