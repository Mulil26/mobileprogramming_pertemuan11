<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class ProductController extends Controller
{
    
    // GET: /api/products
    
    public function index()
    {
        try {
            $products = Product::all();

            // Tambahkan image_url ke setiap produk
            $products->each(function ($product) {
                $product->image_url = $product->image
                    ? asset('storage/' . $product->image)
                    : null;
            });

            return response()->json([
                'success' => true,
                'data'    => $products,
                'message' => 'Data produk berhasil diambil'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    // POST: /api/products
    public function store(Request $request)
    {

        if ($request->isMethod('post')&& $request->hasHeader('Content-Type')){
            $contentType = $request->header('Content-Type');
            if (str_contains($contentType, 'multipart/form-data')){
                
                $data = $request->all();

            }
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'descriptions' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = new Product;
        $product ->name = $request->name;
        $product ->descriptions = $request->descriptions;
        $product ->price = $request->price;
        $product ->stock = $request->stock;


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products','public');
            $product->image = $path;
        }

        $product->save();


        $product->image_url = $product->image 
             ? asset('storage/' . $product->image)
             : null;

        return response()->json([
            'success' => true,
            'data'    => $product,
            'message' => 'Product created successfully'
        ], Response::HTTP_CREATED);
    }

    
    // GET: /api/products/{id}
    
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }


        $product->image_url = $product->image 
            ? asset('storage/' . $product->image) 
            : null;

        return response()->json([
            'success' => true,
            'data'    => $product
        ], Response::HTTP_OK);
    }


    // PUT/PATCH: /api/products/{id}


    public function update(Request $request, string $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // PERBAIKAN 1: Ganti 'description' menjadi 'descriptions'
            $validator = Validator::make($request->all(), [
                'name'         => 'sometimes|required|string|max:255',
                'descriptions' => 'nullable|string', // typo diperbaiki
                'price'        => 'sometimes|required|numeric|min:0',
                'stock'        => 'sometimes|required|integer|min:0',
                'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Simpan data text
            if ($request->has('name')) $product->name = $request->name;
            if ($request->has('descriptions')) $product->descriptions = $request->descriptions; // typo diperbaiki
            if ($request->has('price')) $product->price = $request->price;
            if ($request->has('stock')) $product->stock = $request->stock;

            // Debugging (Opsional: akan muncul di storage/logs/laravel.log)
            Log::info('Update Product ID: ' . $id, [
                'has_file' => $request->hasFile('image'),
                'all_files' => $request->allFiles()
            ]);

            // PERBAIKAN 2: Penanganan Gambar
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                // Simpan gambar baru
                $path = $request->file('image')->store('products', 'public');
                $product->image = $path;        
            }

            $product->save();

            // Refresh Image URL
            $product->image_url = $product->image 
                ? asset('storage/' . $product->image) 
                : null;

            return response()->json([
                'success' => true,
                'data'    => $product,
                'message' => 'Product updated successfully'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }


    // DELETE: /api/products/{id}

    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }


        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }


    // Custom: reduce stock

    public function reduceStock(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $quantity = $request->quantity;

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock
            ], Response::HTTP_BAD_REQUEST);
        }

        $product->stock -= $quantity;
        $product->save();

        $product->image_url = $product->image 
            ? asset('storage/' . $product->image) 
            : null;

        return response()->json([
            'success' => true,
            'data'    => $product,
            'message' => "Stock reduced by $quantity"
        ], Response::HTTP_OK);
    }

    // Custom: upload image
    public function uploadImage(Request $request, $id)
    {
        try {
            Log::info('Upload image called', [
                'product_id' => $id,
                'has_file'   => $request->hasFile('image'),
                'all_files'  => $request->allFiles(),
                'all_input'  => $request->all()
            ]);

            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }


            if (!$request->hasFile('image')) {
                return response()->json([
                    'message'  => 'No image file found',
                    'received' => $request->allFiles()
                ], 400);
            }

            $file = $request->file('image');


            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Uploaded file is not valid',
                    'error'   => $file->getError()
                ], 400);
            }


            $validator = Validator::make(['image' => $file], [
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }


            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }


            $destinationPath = storage_path('app/public/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename     = $originalName . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);

            $product->image = 'products/' . $filename;
            $product->save();

            return response()->json([
                'success'   => true,
                'image_url' => asset('storage/products/' . $filename)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Upload image error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ], 500);
        }
    }
}
