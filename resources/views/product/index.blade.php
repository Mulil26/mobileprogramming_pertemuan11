<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Data Product
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900 min-h-screen"> {{-- background gelap --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-lg overflow-hidden shadow  border-gray-700 p-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Data Product</h3>

                {{-- Tombol Tambah & Cetak PDF --}}
                <div class="flex justify-end mb-4 gap-3">
                    <a href="{{ route('product.create') }}"
                       class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg shadow">
                        + Tambah Product
                    </a>
                    <a href="{{ route('product.cetakPdf') }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg shadow">
                        📄 Cetak PDF
                    </a>
                </div>

                {{-- Notifikasi --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Tabel --}}
                <table class="w-full text-sm text-white divide-y divide-gray-700">
                    <thead class="bg-gray-700 divide-x divide-gray-600">
                        <tr>
                            <th class="p-2 text-center">No</th>
                            <th class="p-2">Nama</th>
                            <th class="p-2 text-center">Gambar</th>
                            <th class="p-2">Deskripsi</th>
                            <th class="p-2 text-right">Harga</th>
                            <th class="p-2 text-center">Stock</th>
                            <th class="p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($products as $row)
                            <tr class="hover:bg-gray-700">
                                <td class="p-2 text-center">{{ $loop->iteration }}</td>
                                <td class="p-2">{{ $row->name }}</td>

                                {{-- Gambar --}}
                                <td class="p-2 text-center">
                                    @if($row->image)
                                        <img src="{{ asset('storage/' . $row->image) }}"
                                             width="80" class="rounded shadow mx-auto">
                                    @else
                                        <span class="text-gray-400">Tidak ada gambar</span>
                                    @endif
                                </td>

                                {{-- Deskripsi --}}
                                <td class="p-2">{{ $row->descriptions }}</td>

                                {{-- Harga --}}
                                <td class="p-2 text-right">Rp {{ number_format($row->price, 0, ',', '.') }}</td>

                                {{-- Stock --}}
                                <td class="p-2 text-center">
                                    @if($row->stock > 0)
                                        <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">{{ $row->stock }}</span>
                                    @else
                                        <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">Habis</span>
                                    @endif
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="p-2 text-center space-x-2">
                                    <a href="{{ route('product.show', $row->id) }}"
                                       class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                        Lihat
                                    </a>
                                    <a href="{{ route('product.edit', $row->id) }}"
                                       class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('product.destroy', $row->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                             Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4 text-gray-400">
                                    Belum ada data product.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
