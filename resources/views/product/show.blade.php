{{-- resources/views/product/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Product
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded shadow">

            {{-- Gambar Produk --}}
            @if($product->image)
                <div class="mb-4 text-center">
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="mx-auto w-64 h-64 object-cover rounded-lg shadow">
                </div>
            @endif

            {{-- Detail Produk --}}
            <div class="space-y-2">
                <p><strong>Nama Produk :</strong> {{ $product->name }}</p>

                <p><strong>File Gambar :</strong>
                    <span class="text-gray-700">{{ $product->image }}</span>
                </p>

                <p><strong>Deskripsi :</strong> {{ $product->descriptions }}</p>

                <p><strong>Harga :</strong>
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <p><strong>Stock :</strong>
                    {{ number_format($product->stock, 0, ',', '.') }}
                </p>
            </div>

            {{-- Tombol Kembali --}}
            <div class="mt-4">
                <a href="{{ route('product.index') }}"
                   class="text-blue-600 hover:text-blue-900">
                    Kembali ke Daftar Product
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
